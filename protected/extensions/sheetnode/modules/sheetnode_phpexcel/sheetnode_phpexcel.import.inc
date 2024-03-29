<?php

function _sheetnode_phpexcel_import_form($form, &$form_state, $format) {
  $form['#attributes'] = array('enctype' => "multipart/form-data");
  $plugins = sheetnode_phpexcel_get_plugins();
  $form['#plugin'] = $plugins[$format];
  $form['import'] = array(
    '#type' => 'file',
    '#title' => t('!format to import', array('!format' => $form['#plugin']['long-name'])),
    '#size' => 40,
  );
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
  );
  return $form;
}

function _sheetnode_phpexcel_import_form_validate($form, &$form_state) {
  $source = $_FILES['files']['name']['import'];

  // Handle PHP file upload errors.
  switch ($_FILES['files']['error']['import']) {
  case UPLOAD_ERR_OK:
    break;
  case UPLOAD_ERR_FORM_SIZE:
  case UPLOAD_ERR_INI_SIZE:
    form_set_error('import', t('The file %file could not be saved, because it exceeds %maxsize, the maximum allowed size for uploads. Please choose a smaller file or notify your site administrator.', 
      array('%file' => $source, '%maxsize' => format_size(file_upload_max_size()))));
    return;
  case UPLOAD_ERR_PARTIAL:
    form_set_error('import', t('The file %file could not be saved, because the upload did not complete. Please try again or notify your site administrator.', array('%file' => $source)));
    return;
  case UPLOAD_ERR_NO_FILE:
    form_set_error('import', t('No file was uploaded. Please choose a file to upload.'));
    return;
  case UPLOAD_ERR_NO_TMP_DIR:
    form_set_error('import', t('The file %file could not be saved, because the temporary upload folder is missing. Please notify your site administrator.', array('%file' => $source)));
    return;
  case UPLOAD_ERR_CANT_WRITE:
    form_set_error('import', t('The file %file could not be saved, because a failure occurred while writing it to the temporary folder. Please notify your site administrator.', array('%file' => $source)));
    return;
  case UPLOAD_ERR_EXTENSION:
    form_set_error('import', t('The file %file could not be saved, because its extension is disallowed. Please notify your site administrator.', array('%file' => $source)));
    return;
  default:
    form_set_error('import', t('The file %file could not be saved. An unknown error %error has occurred. Please notify your site administrator.', 
      array('%file' => $source, '%error' => $_FILES['files']['error']['import'])));
    return;
  }
}

function _sheetnode_phpexcel_import_form_submit($form, &$form_state) {
  $file = file_save_upload('import', array('file_validate_extensions' => array($form['#plugin']['format'])));
  if ($file) {
    $filepath = file_unmanaged_copy($file->uri);
    file_delete($file);
    sheetnode_phpexcel_batch_import(array($_FILES['files']['name']['import'] => drupal_realpath($filepath)), NULL, '_sheetnode_phpexcel_import_callback');
  }
}

function _sheetnode_phpexcel_import_callback($node, $params, &$context) {
  $workbook = unserialize($context['sandbox']['workbook']);
  if ($workbook->getSheetCount() > 1 && module_exists('book')) {
    // Create book if none present.
    if (empty($context['sandbox']['book'])) {
      global $user;
      $book = new StdClass;
      $book->type = 'book';

      node_object_prepare($book);

      $book->title = $workbook->getProperties()->getTitle();
      if (empty($book->title)) {
        $book->title = !empty($context['sandbox']['filename']) ? $context['sandbox']['filename'] : t('Untitled Workbook');
      }
      $book->name = $user->name;
      $book->language = LANGUAGE_NONE;
      $book->book['bid'] = 'new';
      $book->book['plid'] = $book->book['mlid'] = NULL;

      // Let other modules alter the book or do other work.
      drupal_alter('sheetnode_import', $book, $params, $context);

      $book = node_submit($book);
      node_save($book);
      if ($book->nid) {
        $context['results'][] = $book->nid;
        $context['sandbox']['book'] = $book;
      }
    }

    // Mark the book as parent to the sheetnode.
    $book = $context['sandbox']['book'];
    $node->book['bid'] = $book->nid;
    $node->book['plid'] = $book->book['mlid'];
    $node->book['module'] = 'book';
    $node->book['weight'] = $context['sandbox']['current'] - 1;
  }
}

function _sheetnode_phpexcel_batch_import_file($filename, $filepath, $callback, $params, &$context) {
  module_load_include('inc', 'node', 'node.pages');
  set_time_limit(0);
  global $user;

  if (empty($context['sandbox']['workbook'])) {
    // Load workbook and get number of worksheets.
    $workbook = PHPExcel_IOFactory::load($filepath);
    $context['sandbox']['workbook'] = serialize($workbook);
    $context['sandbox']['total'] = $workbook->getSheetCount();
    $context['sandbox']['current'] = 0;
    $context['sandbox']['filename'] = $filename;
  }
  else {
    // Create sheetnode out of current sheet.
    $workbook = unserialize($context['sandbox']['workbook']);
    $sheet = $workbook->getSheet($context['sandbox']['current'] - 1);
    $node = new StdClass;
    $node->type = 'sheetnode';

    node_object_prepare($node);

    $node->title = $sheet->getTitle();
    $node->name = $user->name;
    $node->language = LANGUAGE_NONE;
    $node->sheetnode['value'] = _sheetnode_phpexcel_import_do($workbook, $sheet);
    $node->sheetnode['template'] = NULL;

    // Let other modules alter the sheetnode or do other work.
    if (!empty($callback) && function_exists($callback)) {
      $callback($node, $params, $context);
    }
    drupal_alter('sheetnode_import', $node, $params, $context);

    // Save the sheetnode.
    $node = node_submit($node);
    node_save($node);
    if (!empty($node->nid)) {
      $context['results'][] = $node->nid;
    }
  }

  // Update progress information.
  if ($context['sandbox']['current'] < $context['sandbox']['total']) {
    $sheet = $workbook->getSheet($context['sandbox']['current']);
    $context['message'] = t('Now processing sheet %sheet.', array('%sheet' => $sheet->getTitle()));
    $context['finished'] = $context['sandbox']['current'] / $context['sandbox']['total'];
    $context['sandbox']['current']++;
  }
}

function _sheetnode_phpexcel_batch_import_finished($success, $results, $operations) {
  $batch =& batch_get();
  if (empty($batch['redirect']) && !empty($results)) {
    drupal_goto('node/' . $results[0]);
  }
}

function _sheetnode_phpexcel_import_do($workbook, $sheet) {
  require_once 'socialcalc.inc';
  $sc = array(); // SocialCalc array structure
  $styles = array(); // Styles cache

  // Default values.
  $font = $sheet->getDefaultStyle()->getFont();
  $defaultcolwidth = PHPExcel_Shared_Drawing::cellDimensionToPixels($sheet->getDefaultColumnDimension()->getWidth(), $font);
  if ($defaultcolwidth > 0) {
    $sc['attribs']['defaultcolwidth'] = $defaultcolwidth;
  }
  $defaultrowheight = $sheet->getDefaultRowDimension()->getRowHeight();
  if ($defaultrowheight > 0) {
    $sc['attribs']['defaultrowheight'] = $defaultrowheight;
  }
  $sc['attribs']['lastrow'] = $sheet->getHighestRow();
  $sc['attribs']['lastcol'] = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());

  // Iterate on rows.
  foreach ($rit = $sheet->getRowIterator() as $row) {
    $r = $row->getRowIndex();
    $height = $sheet->getRowDimension($r)->getRowHeight();
    if ($height != -1 && $height != @$sc['attribs']['defaultrowheight']) {
      $sc['rowattribs']['height'][$r] = $height;
    }
    if (!$sheet->getRowDimension($r)->getVisible()) {
      $sc['rowattribs']['hide'][$r] = TRUE;
    }

    // Iterate on cells.
    foreach ($cit = $row->getCellIterator() as $cell) {
      $ca = $cell->getColumn();
      $c = PHPExcel_Cell::columnIndexFromString($ca);
      $width = $sheet->getColumnDimension($ca)->getWidth();
      if ($width != -1) {
        $width = PHPExcel_Shared_Drawing::cellDimensionToPixels($width, $font);
        if ($width != @$sc['attribs']['defaultcolwidth']) {
          $sc['colattribs']['width'][$c] = $width;
        }
      }
      if (!$sheet->getColumnDimension($ca)->getVisible()) {
        $sc['colattribs']['hide'][$c] = TRUE;
      }
      
      _sheetnode_phpexcel_import_cell($workbook, $sheet, $cell, $sc, $styles);
    }
  }

  // Cell merges
  // TODO: Not working in Excel 5 import.
  foreach ($sheet->getMergeCells() as $range) {
    list($first, $last) = explode(':', $range);
    if (!isset($sc['cells'][$first])) continue;
    $firstcr = socialcalc_coord_to_cr($first);
    $lastcr = socialcalc_coord_to_cr($last);
    $sc['cells'][$first]['colspan'] = $lastcr[0] - $firstcr[0] + 1;
    $sc['cells'][$first]['rowspan'] = $lastcr[1] - $firstcr[1] + 1;
  }

  // Names
  foreach ($workbook->getNamedRanges() as $range) {
    if (is_null($range->getScope()) || $sheet->getHashCode() == $range->getScope()->getHashCode()) {
      $sc['names'][$range->getName()] = array(
        'desc' => '',
        'definition' => 
          '=' .
          (!is_null($range->getWorksheet()) && $range->getWorksheet()->getHashCode() != $sheet->getHashCode() ? $range->getWorksheet()->getTitle() . '!' : '') .
          $range->getRange(),
      );
    }
  }
  
  $socialcalc = array(
    'sheet' => $sc,
    'edit' => socialcalc_default_edit($sc),
    'audit' => socialcalc_default_audit($sc),
  );
  return socialcalc_save($socialcalc);
}

function _sheetnode_phpexcel_import_cell($workbook, $sheet, $cell, &$sc, &$styles) {
  $c = array(); // output cell
  
  // Position
  $c['pos'] = array(PHPExcel_Cell::columnIndexFromString($cell->getColumn()), $cell->getRow());

  // Value and type
  switch ($cell->getDataType()) {
  case PHPExcel_Cell_DataType::TYPE_BOOL:
    $c['datavalue'] = (bool)$cell->getValue();
    $c['datatype'] = 'v';
    $c['valuetype'] = 'n';
    break;
  case PHPExcel_Cell_DataType::TYPE_NUMERIC:
    $c['datavalue'] = floatval($cell->getValue());
    $c['datatype'] = 'v';
    $c['valuetype'] = 'n';
    break;
  case PHPExcel_Cell_DataType::TYPE_STRING:
    $text = $cell->getValue();
    if ($text instanceof PHPExcel_RichText) {
      $style = $sheet->getStyle($cell->getCoordinate());
      $defaultfont = $style->getFont();
      $cellData = '';
      foreach ($text->getRichTextElements() as $element) {
        $font = $element instanceof PHPExcel_RichText_Run ? $element->getFont() : $defaultfont;
        $cellData .= '<span style="' . _sheetnode_phpexcel_assembleCSS(_sheetnode_phpexcel_createCSSStyleFont($font)) . '">';

        if ($font->getSuperScript()) {
          $cellData .= '<sup>';
        } else if ($font->getSubScript()) {
          $cellData .= '<sub>';
        }

        // Convert UTF8 data to PCDATA
        $cellText = $element->getText();
        $cellData .= htmlspecialchars($cellText);

        if ($font->getSuperScript()) {
          $cellData .= '</sup>';
        } else if ($font->getSubScript()) {
          $cellData .= '</sub>';
        }

        $cellData .= '</span>';
      }
      $c['datavalue'] = $cellData;
      $c['valuetype'] = 'th';
      $c['datatype'] = 't';
    }
    else if (empty($text)) { // empty text?
      // That's a bug in PHPExcel where a blank cell is implictly converted to a text cell.
      // Convert it back here by leaving the datatype and valuetype blank.
    }
    else { // it is plain text
      $c['datavalue'] = $text;
      $c['valuetype'] = 't';
      $c['datatype'] = 't';
    }
    break;
  case PHPExcel_Cell_DataType::TYPE_FORMULA:
    $c['datatype'] = 'f';
    $c['formula'] = str_replace(',', ';', ltrim($cell->getValue(), '='));
    try {
      $c['datavalue'] = $cell->getCalculatedValue();
    }
    catch (Exception $e) {
      $c['datavalue'] = 0;
      watchdog('sheetnode_phpexcel', '!title: Error importing formula "!formula" at cell !coord.', array(
        '!title' => $sheet->getTitle(),
        '!formula' => $c['formula'], 
        '!coord' => $cell->getCoordinate()
      ), WATCHDOG_WARNING);
    }
    $c['valuetype'] = is_numeric($c['datavalue']) ? 'n' : 't';
    break;
  case PHPExcel_Cell_DataType::TYPE_ERROR:
    $c['errors'] = $cell->getValue();
    break;
  case PHPExcel_Cell_DataType::TYPE_NULL:
    break;
  }

  // Comment
  $comment = $sheet->getComment($cell->getCoordinate());
  if (!empty($comment)) {
    $text = $comment->getText()->getPlainText();
    if (!empty($text)) {
      $c['comment'] = $comment->getText()->getPlainText();
    }
  }

  // Style
  $style = $sheet->getStyle($cell->getCoordinate());
  $hash = $style->getHashCode();
  if (empty($styles[$hash])) {
    // Font
    $font = _sheetnode_phpexcel_get_font($style->getFont());
    if ($font) {
      $styles[$hash]['font'] = _sheetnode_phpexcel_insert_hash($sc, 'font', $font);
    }

    // Color
    $color = _sheetnode_phpexcel_get_color($style->getFont()->getColor());
    if ($color) {
      $styles[$hash]['color'] = _sheetnode_phpexcel_insert_hash($sc, 'color', $color);
    }

    // Background color
    if ($style->getFill()->getFillType() != PHPExcel_Style_Fill::FILL_NONE) {
      $bgcolor = _sheetnode_phpexcel_get_color($style->getFill()->getStartColor());
      if ($bgcolor) {
        $styles[$hash]['bgcolor'] = _sheetnode_phpexcel_insert_hash($sc, 'color', $bgcolor);
      }
    }

    // Borders
    $borders = array(
      'bt' => 'getTop',
      'bl' => 'getLeft',
      'bb' => 'getBottom',
      'br' => 'getRight',
    );
    foreach ($borders as $bi => $method) {
      $border = _sheetnode_phpexcel_get_border($style->getBorders()->$method());
      if ($border) {
        $styles[$hash][$bi] = _sheetnode_phpexcel_insert_hash($sc, 'borderstyle', $border);
      }
    }

    // Cell format (horizontal alignment)
    $cellformat = _sheetnode_phpexcel_get_cellformat($style);
    if ($cellformat) {
      $styles[$hash]['cellformat'] = _sheetnode_phpexcel_insert_hash($sc, 'cellformat', $cellformat);
    }

    // Layout (padding + vertical alignment)
    $layout = _sheetnode_phpexcel_get_layout($style);
    if ($layout) {
      $styles[$hash]['layout'] = _sheetnode_phpexcel_insert_hash($sc, 'layout', $layout);
    }

    // Number format
    $ntvf = _sheetnode_phpexcel_get_valueformat($style->getNumberFormat());
    if ($ntvf) {
      $styles[$hash]['nontextvalueformat'] = _sheetnode_phpexcel_insert_hash($sc, 'valueformat', $ntvf);
    }
  }
  $c += $styles[$hash]; // merge the cached styles into the cell directly.

  $sc['cells'][$cell->getCoordinate()] = $c;
}

/**
 * LIFTED FROM PHPExcel/Classes/PHPExcel/Writer/HTML.php
 * Takes array where of CSS properties / values and converts to CSS string
 *
 * @param array
 * @return string
 */
function _sheetnode_phpexcel_assembleCSS($pValue = array())
{
  $pairs = array();
  foreach ($pValue as $property => $value) {
    $pairs[] = $property . ':' . $value;
  }
  $string = implode('; ', $pairs);

  return $string;
}

/**
 * LIFTED FROM PHPExcel/Classes/PHPExcel/Writer/HTML.php
 * Create CSS style (PHPExcel_Style_Font)
 *
 * @param PHPExcel_Style_Font     $pStyle     PHPExcel_Style_Font
 * @return  array
 */
function _sheetnode_phpexcel_createCSSStyleFont(PHPExcel_Style_Font $pStyle) {
  // Construct CSS
  $css = array();

  // Create CSS
  if ($pStyle->getBold()) {
    $css['font-weight'] = 'bold';
  }
  if ($pStyle->getUnderline() != PHPExcel_Style_Font::UNDERLINE_NONE && $pStyle->getStrikethrough()) {
    $css['text-decoration'] = 'underline line-through';
  } else if ($pStyle->getUnderline() != PHPExcel_Style_Font::UNDERLINE_NONE) {
    $css['text-decoration'] = 'underline';
  } else if ($pStyle->getStrikethrough()) {
    $css['text-decoration'] = 'line-through';
  }
  if ($pStyle->getItalic()) {
    $css['font-style'] = 'italic';
  }

  $css['color']   = '#' . $pStyle->getColor()->getRGB();
  $css['font-family'] = $pStyle->getName();
  $css['font-size'] = $pStyle->getSize() . 'pt';

  // Return
  return $css;
}

function _sheetnode_phpexcel_insert_hash(&$sc, $key, $style) {
  $hash = @$sc[$key.'hash'][$style];
  if (is_null($hash)) {
    $hash = count(@$sc[$key.'s']) + 1;
    $sc[$key.'s'][$hash] = $style;
    $sc[$key.'hash'][$style] = $hash;
  }
  return $hash;
}

function _sheetnode_phpexcel_get_font($font) {
  return ($font->getItalic() ? 'italic' : 'normal') .' '.
         ($font->getBold() ? 'bold' : 'normal') .' '.
         ($font->getSize() .'pt') .' '.
         ($font->getName());
}

function _sheetnode_phpexcel_get_border($border) {
  $borderstyles = array(
    PHPExcel_Style_Border::BORDER_NONE => FALSE,
    PHPExcel_Style_Border::BORDER_THIN => 'thin solid', 
    PHPExcel_Style_Border::BORDER_MEDIUM => 'medium solid', 
    PHPExcel_Style_Border::BORDER_DASHED => 'thin dashed', 
    PHPExcel_Style_Border::BORDER_DOTTED => 'thin dotted', 
    PHPExcel_Style_Border::BORDER_THICK => 'thick solid', 
    PHPExcel_Style_Border::BORDER_DOUBLE => 'thin double', 
    PHPExcel_Style_Border::BORDER_HAIR => 'thin dotted',  // TODO: is it?
    PHPExcel_Style_Border::BORDER_MEDIUMDASHED => 'medium dashed', 
    PHPExcel_Style_Border::BORDER_DASHDOT => 'thin dashed', // TODO: is it?
    PHPExcel_Style_Border::BORDER_MEDIUMDASHDOT => 'medium dashed', //TODO: is it?
    PHPExcel_Style_Border::BORDER_DASHDOTDOT => 'thin dashed', // TODO: is it?
    PHPExcel_Style_Border::BORDER_MEDIUMDASHDOTDOT => 'medium dashed', // TODO: is it?
    PHPExcel_Style_Border::BORDER_SLANTDASHDOT => 'thin dashed', // TODO: is it?
  );
  $borderstyle = $borderstyles[$border->getBorderStyle()];
  if (!$borderstyle) return FALSE;
  $bordercolor = _sheetnode_phpexcel_get_color($border->getColor());
  return $borderstyle .' '. $bordercolor;
}

function _sheetnode_phpexcel_get_cellformat($style) {
  $cellformats = array(
    PHPExcel_Style_Alignment::HORIZONTAL_GENERAL => FALSE,
    PHPExcel_Style_Alignment::HORIZONTAL_LEFT => 'left',
    PHPExcel_Style_Alignment::HORIZONTAL_CENTER => 'center',
    PHPExcel_Style_Alignment::HORIZONTAL_RIGHT => 'right',
    PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY => 'justify',
    PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS => 'center', // TODO: is it?
  );
  return $cellformats[$style->getAlignment()->getHorizontal()];
}

function _sheetnode_phpexcel_get_layout($style) {
  $valignments = array(
    PHPExcel_Style_Alignment::VERTICAL_TOP => 'top',
    PHPExcel_Style_Alignment::VERTICAL_CENTER => 'middle',
    PHPExcel_Style_Alignment::VERTICAL_BOTTOM => 'bottom', 
    PHPExcel_Style_Alignment::VERTICAL_JUSTIFY => 'middle', // TODO: is it?
  );
  $valignment = $valignments[$style->getAlignment()->getVertical()];
  $padding_left = $style->getAlignment()->getIndent();
  $padding_left = empty($padding_left) ? '*' : PHPExcel_Shared_Drawing::pointsToPixels($padding_left) . 'px';
  return 'padding:* * * '. $padding_left .';vertical-align:'. $valignment .';';
}

function _sheetnode_phpexcel_get_valueformat($numberformat) {
  $valueformat = $numberformat->getFormatCode();
  if (strtolower($valueformat) == 'general') return FALSE;
  return $valueformat;
}

function _sheetnode_phpexcel_get_color($color) {
  $rgb = $color->getRGB();
  return 'rgb(' . hexdec(substr($rgb, 0, 2)) .','. hexdec(substr($rgb, 2, 2)) .','. hexdec(substr($rgb, 4, 2)) .')';
}

