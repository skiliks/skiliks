<?php

/**
 * Smarty Internal Plugin Compile Block
 *
 * Compiles the {block}{/block} tags
 *
 * @package Smarty
 * @subpackage Compiler
 * @author Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile Block Class
 *
 * @package Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Block extends Smarty_Internal_CompileBase {

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $required_attributes = array('name');

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $shorttag_order = array('name', 'hide');

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = array('hide');

    /**
     * Compiles code for the {block} tag
     *
     * @param array  $args     array with attributes from parser
     * @param object $compiler compiler object
     * @return boolean true
     */
    public function compile($args, $compiler) {
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);
        $save = array($_attr, $compiler->parser->current_buffer, $compiler->nocache, $compiler->smarty->merge_compiled_includes, $compiler->merged_templates, $compiler->smarty->merged_templates_func, $compiler->template->properties, $compiler->template->has_nocache_code);
        $this->openTag($compiler, 'block', $save);
        if ($_attr['nocache'] == true) {
            $compiler->nocache = true;
        }
        // set flag for {block} tag
        $compiler->inheritance = true;
        // must merge includes
        $compiler->smarty->merge_compiled_includes = true;

        $compiler->parser->current_buffer = new _smarty_template_buffer($compiler->parser);
        $compiler->has_code = false;
        return true;
    }

    /**
     * Save or replace child block source by block name during parsing
     *
     * @param string $block_content     block source content
     * @param string $block_tag         opening block tag
     * @param object $template          template object
     * @param string $filepath          filepath of template source
     */
    public static function saveBlockData($block_content, $block_tag, $template, $filepath) {
        $_rdl = preg_quote($template->smarty->right_delimiter);
        $_ldl = preg_quote($template->smarty->left_delimiter);
        if (!$template->smarty->auto_literal) {
            $al = '\s*';
        } else {
            $al = '';
        }
        if (0 == preg_match("!({$_ldl}{$al}block\s+)(name=)?(\w+|'.*'|\".*\")(\s*?)?((append|prepend|nocache)?(\s*)?(hide)?)?(\s*{$_rdl})!", $block_tag, $_match)) {
            $error_text = 'Syntax Error in template "' . $template->source->filepath . '"   "' . htmlspecialchars($block_tag) . '" illegal options';
            throw new SmartyCompilerException($error_text);
        } else {
            $_name = trim($_match[3], '\'"');
            if ($_match[8] != 'hide' || isset($template->block_data[$_name])) {        // replace {$smarty.block.child}
                // do we have {$smart.block.child} in nested {block} tags?
                if (0 != preg_match_all("!({$_ldl}{$al}block\s+)(name=)?(\w+|'.*'|\".*\")([\s\S]*?)(hide)?(\s*{$_rdl})([\s\S]*?)({$_ldl}{$al}\\\$smarty\.block\.child\s*{$_rdl})([\s\S]*?{$_ldl}{$al}/block\s*{$_rdl})!", $block_content, $_match2)) {
                    foreach ($_match2[3] as $key => $name) {
                        // get it's replacement
                        $_name2 = trim($name, '\'"');
                        if ($_match2[5][$key] != 'hide' || isset($template->block_data[$_name2])) {
                            if (isset($template->block_data[$_name2])) {
                                $replacement = $template->block_data[$_name2]['source'];
                            } else {
                                $replacement = '';
                            }
                            // replace {$smarty.block.child} tag
