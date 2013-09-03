//
// SocialCalcSpreadsheetControl
//
/*
// The code module of the SocialCalc package that lets you embed a spreadsheet
// control with toolbar, etc., into a web page.
//
// (c) Copyright 2008, 2009, 2010 Socialtext, Inc.
// All Rights Reserved.
//
*/

/*

LEGAL NOTICES REQUIRED BY THE COMMON PUBLIC ATTRIBUTION LICENSE:

EXHIBIT A. Common Public Attribution License Version 1.0.

The contents of this file are subject to the Common Public Attribution License Version 1.0 (the 
"License"); you may not use this file except in compliance with the License. You may obtain a copy 
of the License at http://socialcalc.org. The License is based on the Mozilla Public License Version 1.1 but 
Sections 14 and 15 have been added to cover use of software over a computer network and provide for 
limited attribution for the Original Developer. In addition, Exhibit A has been modified to be 
consistent with Exhibit B.

Software distributed under the License is distributed on an "AS IS" basis, WITHOUT WARRANTY OF ANY 
KIND, either express or implied. See the License for the specific language governing rights and 
limitations under the License.

The Original Code is SocialCalc JavaScript SpreadsheetControl.

The Original Developer is the Initial Developer.

The Initial Developer of the Original Code is Socialtext, Inc. All portions of the code written by 
Socialtext, Inc., are Copyright (c) Socialtext, Inc. All Rights Reserved.

Contributor: Dan Bricklin.


EXHIBIT B. Attribution Information

When the SpreadsheetControl is producing and/or controlling the display the Graphic Image must be
displayed on the screen visible to the user in a manner comparable to that in the 
Original Code. The Attribution Phrase must be displayed as a "tooltip" or "hover-text" for
that image. The image must be linked to the Attribution URL so as to access that page
when clicked. If the user interface includes a prominent "about" display which includes
factual prominent attribution in a form similar to that in the "about" display included
with the Original Code, including Socialtext copyright notices and URLs, then the image
need not be linked to the Attribution URL but the "tool-tip" is still required.

Attribution Copyright Notice:

 Copyright (C) 2010 Socialtext, Inc.
 All Rights Reserved.

Attribution Phrase (not exceeding 10 words): SocialCalc

Attribution URL: http://www.socialcalc.org/

Graphic Image: The contents of the sc-logo.gif file in the Original Code or
a suitable replacement from http://www.socialcalc.org/licenses specified as
being for SocialCalc.

Display of Attribution Information is required in Larger Works which are defined 
in the CPAL as a work which combines Covered Code or portions thereof with code 
not governed by the terms of the CPAL.

*/

//
// Some of the other files in the SocialCalc package are licensed under
// different licenses. Please note the licenses of the modules you use.
//
// Code History:
//
// Initially coded by Dan Bricklin of Software Garden, Inc., for Socialtext, Inc.
// Unless otherwise specified, referring to "SocialCalc" in comments refers to this
// JavaScript version of the code, not the SocialCalc Perl code.
//

/*

See the comments in the main SocialCalc code module file of the SocialCalc package.

*/

   var SocialCalc;
   if (!SocialCalc) {
      alert("Main SocialCalc code module needed");
      SocialCalc = {};
      }
   if (!SocialCalc.TableEditor) {
      alert("SocialCalc TableEditor code module needed");
      }

// *************************************
//
// SpreadsheetControl class:
//
// *************************************

// Global constants:

   SocialCalc.CurrentSpreadsheetControlObject = null; // right now there can only be one active at a time
    SocialCalc.SpreadsheetControlObjects = {};

// Constructor:

SocialCalc.SpreadsheetControl = function(idPrefix) {

   var scc = SocialCalc.Constants;

   // Properties:

   this.parentNode = null;
   this.spreadsheetDiv = null;
   this.requestedHeight = 0;
   this.requestedWidth = 0;
   this.requestedSpaceBelow = 0;
   this.height = 0;
   this.width = 0;
   this.viewheight = 0; // calculated amount for views below toolbar, etc.

   // Dynamic properties:

   this.sheet = null;
   this.context = null;
   this.editor = null;

   this.spreadsheetDiv = null;
   this.editorDiv = null;

   this.sortrange = ""; // remembered range for sort tab

   this.moverange = ""; // remembered range from movefrom used by movepaste/moveinsert

   // Constants:
   this.idPrefix = idPrefix || "SocialCalc-"; // prefix added to element ids used here, should end in "-"
   this.multipartBoundary = "SocialCalcSpreadsheetControlSave"; // boundary used by SpreadsheetControlCreateSpreadsheetSave
   this.imagePrefix = scc.defaultImagePrefix; // prefix added to img src

   this.toolbarbackground = scc.SCToolbarbackground;

   // Callbacks:
   this.ExportCallback = null; // a function called for Clipboard Export button: this.ExportCallback(spreadsheet_control_object)

   // Initialization Code:
   this.sheet = new SocialCalc.Sheet();
   this.context = new SocialCalc.RenderContext(this.sheet);
   this.context.showGrid=true;
   this.context.showRCHeaders=true;
   this.editor = new SocialCalc.TableEditor(this.context);
   this.editor.StatusCallback.statusline =
      {func: SocialCalc.SpreadsheetControlStatuslineCallback,
       params: {statuslineid: this.idPrefix+"statusline",
                recalcid1: this.idPrefix+"divider_recalc",
                recalcid2: this.idPrefix+"button_recalc"}};

   SocialCalc.CurrentSpreadsheetControlObject = this; // remember this for rendezvousing on events
   SocialCalc.SpreadsheetControlObjects[this.idPrefix] = this; // remember this for rendezvousing on events

   this.editor.MoveECellCallback.movefrom = function(editor) {
      var cr;
      var spreadsheet = SocialCalc.GetSpreadsheetControlObject(editor.idPrefix);
      spreadsheet.context.cursorsuffix = "";
      if (editor.range2.hasrange && !editor.cellhandles.noCursorSuffix) {
         if (editor.ecell.row==editor.range2.top && (editor.ecell.col<editor.range2.left || editor.ecell.col>editor.range2.right+1)) {
            spreadsheet.context.cursorsuffix = "insertleft";
         }
         if (editor.ecell.col==editor.range2.left && (editor.ecell.row<editor.range2.top || editor.ecell.row>editor.range2.bottom+1)) {
            spreadsheet.context.cursorsuffix = "insertup";
         }
     }
   };
};

// Methods:

SocialCalc.SpreadsheetControl.prototype.InitializeSpreadsheetControl =
   function(node, height, width, spacebelow) {return SocialCalc.InitializeSpreadsheetControl(this, node, height, width, spacebelow);};
SocialCalc.SpreadsheetControl.prototype.DoOnResize = function() {return SocialCalc.DoOnResize(this);};
SocialCalc.SpreadsheetControl.prototype.SizeSSDiv = function() {return SocialCalc.SizeSSDiv(this);};
SocialCalc.SpreadsheetControl.prototype.ExecuteCommand = 
   function(combostr, sstr) {return SocialCalc.SpreadsheetControlExecuteCommand(this, combostr, sstr);};
SocialCalc.SpreadsheetControl.prototype.CreateSheetHTML = 
   function() {return SocialCalc.SpreadsheetControlCreateSheetHTML(this);};
SocialCalc.SpreadsheetControl.prototype.CreateSpreadsheetSave = 
   function(otherparts) {return SocialCalc.SpreadsheetControlCreateSpreadsheetSave(this, otherparts);};
SocialCalc.SpreadsheetControl.prototype.DecodeSpreadsheetSave = 
   function(str) {return SocialCalc.SpreadsheetControlDecodeSpreadsheetSave(this, str);};
SocialCalc.SpreadsheetControl.prototype.CreateCellHTML = 
   function(coord) {return SocialCalc.SpreadsheetControlCreateCellHTML(this, coord);};
SocialCalc.SpreadsheetControl.prototype.CreateCellHTMLSave = 
   function(range) {return SocialCalc.SpreadsheetControlCreateCellHTMLSave(this, range);};


// Sheet Methods to make things a little easier:

SocialCalc.SpreadsheetControl.prototype.ParseSheetSave = function(str) {return this.sheet.ParseSheetSave(str);};
SocialCalc.SpreadsheetControl.prototype.CreateSheetSave = function() {return this.sheet.CreateSheetSave();};


// Functions:

//
// InitializeSpreadsheetControl(spreadsheet, node, height, width, spacebelow)
//
// Creates the control elements and makes them the child of node (string or element).
// If present, height and width specify size.
// If either is 0 or null (missing), the maximum that fits on the screen
// (taking spacebelow into account) is used.
//
// Displays the tabs and creates the views (other than "sheet").
// The first tab is set as selected, but onclick is not invoked.
//
// You should do a redisplay or recalc (which redisplays) after running this.
//

SocialCalc.InitializeSpreadsheetControl = function(spreadsheet, node, height, width, spacebelow) {

   var scc = SocialCalc.Constants;
   var SCLoc = SocialCalc.LocalizeString;
   var SCLocSS = SocialCalc.LocalizeSubstrings;

   var html, child, i, vname, v, style, button, bele;

   spreadsheet.requestedHeight = height;
   spreadsheet.requestedWidth = width;
   spreadsheet.requestedSpaceBelow = spacebelow;

   if (typeof node == "string") node = document.getElementById(node);

   if (node === null) {
      alert("SocialCalc.SpreadsheetControl not given parent node.");
      }

   spreadsheet.parentNode = node;

   // create node to hold spreadsheet control

   spreadsheet.spreadsheetDiv = document.createElement("div");

   spreadsheet.SizeSSDiv(); // calculate and fill in the size values

   for (child=node.firstChild; child!==null; child=node.firstChild) {
      node.removeChild(child);
      }

   html = '<div class="toolbar" style="'+spreadsheet.toolbarbackground + 'padding: 0px 10px 10px 4px; height:45px;">' +
        '<ul class="button_menu">' +
        '<li><div class="grid-row"><div class="hover-wrap"><div class="menu-hover"><img id="%id.button_undo"    class="button-undo"    src="%img.undo.png" style=""></div></div></div><a class="grid-row">отменить</a></li>' +
        '<li><div class="grid-row"><div class="hover-wrap"><div class="menu-hover"><img id="%id.button_redo"    class="button-redo"    src="%img.redo.png" style=""></div></div></div><a class="grid-row">вернуть</a></li>' +
        '<li><div class="grid-row"><div class="hover-wrap"><div class="menu-hover"><img id="%id.button_copy"    class="button-copy"    src="%img.copy.png" style=""></div></div></div><a class="grid-row">копировать</a></li>' +
        '<li><div class="grid-row"><div class="hover-wrap"><div class="menu-hover"><img id="%id.button_paste"   class="button-paste"   src="%img.paste.png" style=""></div></div></div><a class="grid-row">вставить</a></li>' +
        '<li><div class="grid-row"><div class="hover-wrap"><div class="menu-hover"><img id="%id.button_sum"     class="button-sum"     src="%img.auto_sum.png" style=""></div></div></div><a class="grid-row">сумма</a></li>' +
        '<li><div class="grid-row"><div class="hover-wrap"><div class="menu-hover"><img id="%id.button_percent" class="button-percent" src="%img.percent.png" alt="%" style=""></div></div></div><a class="grid-row">формат</a></li></ul>' +
        '<ul class="menu_bar"><li><input class="status" id="%id.statusline" type="text" disabled="disabled" value="" /></span></li>' +
        '<li><div class="grid-row"><div class="hover-wrap"><div class="menu-hover"><img id="%id.button_function" src="%img.function.png" style=""></div></div></div></li>' +
        '<li><input class="formula" id="%id.formula_field" type="text" size="100" value="" /></li></ul>' +
    '</div>';

   html = html.replace(/\%s\./g, "SocialCalc.");
   html = html.replace(/\%id\./g, spreadsheet.idPrefix);
   html = html.replace(/\%tbt\./g, spreadsheet.toolbartext);
   html = html.replace(/\%img\./g, spreadsheet.imagePrefix);
   html = SCLocSS(html); // localize with %loc!string! and %scc!constant!

   spreadsheet.spreadsheetDiv.innerHTML = html;
   node.appendChild(spreadsheet.spreadsheetDiv);

   // Initialize SocialCalc buttons

   spreadsheet.Buttons = {
       button_undo: {tooltip: "Undo", command: "undo"},
       button_redo: {tooltip: "Redo", command: "redo"},
       button_copy: {tooltip: "Copy", command: "copy"},
       button_paste: {tooltip: "Paste", command: "paste"},
       button_sum: {tooltip: "Autosum", command: "sum"},
       button_percent: {tooltip: "Format as percent", command: "percent"},
       button_function: {tooltip: "Functions", command: "function-list"}
   };

   for (button in spreadsheet.Buttons) {
      bele = document.getElementById(spreadsheet.idPrefix+button);
      if (!bele) {alert("Button "+(spreadsheet.idPrefix+button)+" missing"); continue;}
      bele.setAttribute('data-spreadsheet', spreadsheet.idPrefix);
      SocialCalc.TooltipRegister(bele, SCLoc(spreadsheet.Buttons[button].tooltip), {});
      SocialCalc.ButtonRegister(bele,
         {normalstyle: "",
          hoverstyle: "",
          downstyle: ""},
         {MouseDown: SocialCalc.DoButtonCmd, command: spreadsheet.Buttons[button].command});
   }

   // create formula field
   var inputbox = new SocialCalc.InputBox(document.getElementById(spreadsheet.idPrefix+'formula_field'), spreadsheet.editor);

   // create sheet view and others
   spreadsheet.nonviewheight = spreadsheet.spreadsheetDiv.firstChild.offsetHeight;
   spreadsheet.viewheight = spreadsheet.height-spreadsheet.nonviewheight;
   spreadsheet.editorDiv=spreadsheet.editor.CreateTableEditor(spreadsheet.width, spreadsheet.viewheight);

   spreadsheet.spreadsheetDiv.appendChild(spreadsheet.editorDiv);

   // done - refresh screen needed

   return;

   }

//
// outstr = SocialCalc.LocalizeString(str)
//
// SocialCalc function to make localization easier.
// If str is "Text to localize", it returns
// SocialCalc.Constants.s_loc_text_to_localize if
// it exists, or else with just "Text to localize".
// Note that spaces are replaced with "_" and other special
// chars with "X" in the name of the constant (e.g., "A & B"
// would look for SocialCalc.Constants.s_loc_a_X_b.
//

SocialCalc.LocalizeString = function(str) {
   var cstr = SocialCalc.LocalizeStringList[str]; // found already this session?
   if (!cstr) { // no - look up
      cstr = SocialCalc.Constants["s_loc_"+str.toLowerCase().replace(/\s/g, "_").replace(/\W/g, "X")] || str;
      SocialCalc.LocalizeStringList[str] = cstr;
      }
   return cstr;
   }

SocialCalc.LocalizeStringList = {}; // a list of strings to localize accumulated by the routine

//
// outstr = SocialCalc.LocalizeSubstrings(str)
//
// SocialCalc function to make localization easier using %loc and %scc.
//
// Replaces sections of str with:
//    %loc!Text to localize!
// with SocialCalc.Constants.s_loc_text_to_localize if
// it exists, or else with just "Text to localize".
// Note that spaces are replaced with "_" and other special
// chars with "X" in the name of the constant (e.g., %loc!A & B!
// would look for SocialCalc.Constants.s_loc_a_X_b.
// Uses SocialCalc.LocalizeString for this.
//
// Replaces sections of str with:
//    %ssc!constant-name!
// with SocialCalc.Constants.constant-name.
// If the constant doesn't exist, throws and alert.
//

SocialCalc.LocalizeSubstrings = function(str) {

   var SCLoc = SocialCalc.LocalizeString;

   return str.replace(/%(loc|ssc)!(.*?)!/g, function(a, t, c) {
      if (t=="ssc") {
         return SocialCalc.Constants[c] || alert("Missing constant: "+c);
         }
      else {
         return SCLoc(c);
         }
      });

   }

//
// obj = GetSpreadsheetControlObject()
//
// Returns the current spreadsheet control object
//

SocialCalc.GetSpreadsheetControlObject = function(id) {

    if (id === undefined) {
        console.warn('GetSpreadsheetControlObject MUST have id argument to work properly')
        console.trace();
       var csco = SocialCalc.CurrentSpreadsheetControlObject;
       if (csco) return csco;
    } else {
        return SocialCalc.SpreadsheetControlObjects[id];
    }

//   throw ("No current SpreadsheetControl object.");

   }


//
// SocialCalc.DoOnResize(spreadsheet)
//
// Processes an onResize event, setting the different views.
//

SocialCalc.DoOnResize = function(spreadsheet) {

   var needresize = spreadsheet.SizeSSDiv();
   if (!needresize) return;


   var view = spreadsheet.editorDiv;
   view.style.width = spreadsheet.width + "px";
   view.style.height = (spreadsheet.height-spreadsheet.nonviewheight) + "px";

   spreadsheet.editor.ResizeTableEditor(spreadsheet.width, spreadsheet.height-spreadsheet.nonviewheight);

   }


//
// resized = SocialCalc.SizeSSDiv(spreadsheet)
//
// Figures out a reasonable size for the spreadsheet, given any requested values and viewport.
// Sets ssdiv to that.
// Return true if different than existing values.
//

SocialCalc.SizeSSDiv = function(spreadsheet) {

   var sizes, pos, resized, nodestyle, newval;
   var fudgefactorX = 10; // for IE
   var fudgefactorY = 10;

   resized = false;

   sizes = SocialCalc.GetViewportInfo();
   pos = SocialCalc.GetElementPosition(spreadsheet.parentNode);
   pos.bottom = 0;
   pos.right = 0;

   nodestyle = spreadsheet.parentNode.style;

   if (nodestyle.marginTop) {
      pos.top += nodestyle.marginTop.slice(0,-2)-0;
      }
   if (nodestyle.marginBottom) {
      pos.bottom += nodestyle.marginBottom.slice(0,-2)-0;
      }
   if (nodestyle.marginLeft) {
      pos.left += nodestyle.marginLeft.slice(0,-2)-0;
      }
   if (nodestyle.marginRight) {
      pos.right += nodestyle.marginRight.slice(0,-2)-0;
      }

   newval = spreadsheet.requestedHeight ||
            sizes.height - (pos.top + pos.bottom + fudgefactorY) -
               (spreadsheet.requestedSpaceBelow || 0);

   spreadsheet.height = newval;
   spreadsheet.spreadsheetDiv.style.height = newval + "px";

   newval = spreadsheet.requestedWidth ||
            sizes.width - (pos.left + pos.right + fudgefactorX) || 700;

   spreadsheet.width = newval;
   spreadsheet.spreadsheetDiv.style.width = newval + "px";

   return true;

};


//
// SocialCalc.SetTab(obj)
//
// The obj argument is either a string with the tab name or a DOM element with an ID
//

SocialCalc.SetTab = function(obj, sheet) {

   var newtab, tname, newtabnum, newview, i, vname, ele;
   var menutabs = {};
   var tools = {};

   var spreadsheet = SocialCalc.GetSpreadsheetControlObject(obj.getAttribute('data-spreadsheet'));
   var tabs = spreadsheet.tabs;
   var views = spreadsheet.views;

   if (typeof sheet == "string") {
      newtab = sheet;
      }
   else {
      newtab = obj.id.slice(spreadsheet.idPrefix.length,-3);
      }

   if (spreadsheet.editor.busy && // if busy and switching from "sheet", ignore
         (!tabs[spreadsheet.currentTab].view || tabs[spreadsheet.currentTab].view=="sheet")) {
      for (i=0; i<tabs.length; i++) {
         if(tabs[i].name==newtab && (tabs[i].view && tabs[i].view!="sheet")) {
            return;
            }
         }
      }

   if (spreadsheet.tabs[spreadsheet.currentTab].onunclick) {
      spreadsheet.tabs[spreadsheet.currentTab].onunclick(spreadsheet, spreadsheet.tabs[spreadsheet.currentTab].name);
      }

   for (i=0; i<tabs.length; i++) {
      tname = tabs[i].name;
      menutabs[tname] = document.getElementById(spreadsheet.idPrefix+tname+"tab");
      tools[tname] = document.getElementById(spreadsheet.idPrefix+tname+"tools");
      if (tname==newtab) {
         newtabnum = i;
         tools[tname].style.display = "block";
         menutabs[tname].style.cssText = spreadsheet.tabselectedCSS;
         }
      else {
         tools[tname].style.display = "none";
         menutabs[tname].style.cssText = spreadsheet.tabplainCSS;
         }
      }

   spreadsheet.currentTab = newtabnum;

   if (tabs[newtabnum].onclick) {
      tabs[newtabnum].onclick(spreadsheet, newtab);
      }

   for (vname in views) {
      if ((!tabs[newtabnum].view && vname == "sheet") || tabs[newtabnum].view == vname) {
         views[vname].element.style.display = "block";
         newview = vname;
         }
      else {
         views[vname].element.style.display = "none";
         }
      }

   if (tabs[newtabnum].onclickFocus) {
      ele = tabs[newtabnum].onclickFocus;
      if (typeof ele == "string") {
         ele = document.getElementById(spreadsheet.idPrefix+ele);
         ele.focus();
         }
      SocialCalc.CmdGotFocus(ele);
      }
   else {
      SocialCalc.KeyboardFocus();
      }

   if (views[newview].needsresize && views[newview].onresize) {
      views[newview].needsresize = false;
      views[newview].onresize(spreadsheet, views[newview]);
      }

   return;

   }

//
// SocialCalc.SpreadsheetControlStatuslineCallback
//

SocialCalc.SpreadsheetControlStatuslineCallback = function(editor, status, arg, params) {

   var rele1, rele2;

   var ele = document.getElementById(params.statuslineid);

   if (ele) {
      ele.value = editor.GetStatuslineString(status, arg, params);
      }

   switch (status) {
      case "cmdendnorender":
      case "calcfinished":
      case "doneposcalc":
         //console.log('SC status (1): ', status);
         rele1 = document.getElementById(params.recalcid1);
         rele2 = document.getElementById(params.recalcid2);
         if (!rele1 || !rele2) break;
         if (editor.context.sheetobj.attribs.needsrecalc=="yes") {
            rele1.style.display = "inline";
            rele2.style.display = "inline";
            }
         else {
            rele1.style.display = "none";
            rele2.style.display = "none";
            }
         break;

      default:
         break;
      }

   }


//
// SocialCalc.UpdateSortRangeProposal(editor)
//
// Updates sort range proposed in the UI in element idPrefix+sortlist
//

SocialCalc.UpdateSortRangeProposal = function(editor) {

   var ele = document.getElementById(SocialCalc.GetSpreadsheetControlObject().idPrefix+"sortlist");
   if (editor.range.hasrange) {
      ele.options[0].text = SocialCalc.crToCoord(editor.range.left, editor.range.top) + ":" +
                            SocialCalc.crToCoord(editor.range.right, editor.range.bottom);
      }
   else {
      ele.options[0].text = SocialCalc.LocalizeString("[select range]");
      }

   }

//
// SocialCalc.LoadColumnChoosers(spreadsheet)
//
// Updates list of columns for choosing which to sort for Major, Minor, and Last sort
//

SocialCalc.LoadColumnChoosers = function(spreadsheet) {

   var SCLoc = SocialCalc.LocalizeString;

   var sortrange, nrange, rparts, col, colname, sele, oldindex;

   if (spreadsheet.sortrange && spreadsheet.sortrange.indexOf(":")==-1) { // sortrange is a named range
      nrange = SocialCalc.Formula.LookupName(spreadsheet.sheet, spreadsheet.sortrange || "");
      if (nrange.type == "range") {
         rparts = nrange.value.match(/^(.*)\|(.*)\|$/);
         sortrange = rparts[1] + ":" + rparts[2];
         }
      else {
         sortrange = "A1:A1";
         }
      }
   else {
      sortrange = spreadsheet.sortrange;
      }
   var range = SocialCalc.ParseRange(sortrange);
   sele = document.getElementById(spreadsheet.idPrefix+"majorsort");
   oldindex = sele.selectedIndex;
   sele.options.length = 0;
   sele.options[sele.options.length] = new Option(SCLoc("[None]"), "");
   for (var col=range.cr1.col; col<=range.cr2.col; col++) {
      colname = SocialCalc.rcColname(col);
      sele.options[sele.options.length] = new Option(SCLoc("Column ")+colname, colname);
      }
   sele.selectedIndex = oldindex > 1 && oldindex <= (range.cr2.col-range.cr1.col+1) ? oldindex : 1; // restore what was there if reasonable
   sele = document.getElementById(spreadsheet.idPrefix+"minorsort");
   oldindex = sele.selectedIndex;
   sele.options.length = 0;
   sele.options[sele.options.length] = new Option(SCLoc("[None]"), "");
   for (var col=range.cr1.col; col<=range.cr2.col; col++) {
      colname = SocialCalc.rcColname(col);
      sele.options[sele.options.length] = new Option(colname, colname);
      }
   sele.selectedIndex = oldindex > 0 && oldindex <= (range.cr2.col-range.cr1.col+1) ? oldindex : 0; // default to [none]
   sele = document.getElementById(spreadsheet.idPrefix+"lastsort");
   oldindex = sele.selectedIndex;
   sele.options.length = 0;
   sele.options[sele.options.length] = new Option(SCLoc("[None]"), "");
   for (var col=range.cr1.col; col<=range.cr2.col; col++) {
      colname = SocialCalc.rcColname(col);
      sele.options[sele.options.length] = new Option(colname, colname);
      }
   sele.selectedIndex = oldindex > 0 && oldindex <= (range.cr2.col-range.cr1.col+1) ? oldindex : 0; // default to [none]

   }

//
// SocialCalc.CmdGotFocus(obj)
//
// Sets SocialCalc.Keyboard.passThru: obj should be element with focus or "true"
//

SocialCalc.CmdGotFocus = function(obj) {

   SocialCalc.Keyboard.passThru = obj;

   }


//
// SocialCalc.DoButtonCmd(e, buttoninfo, bobj)
//

SocialCalc.DoButtonCmd = function(e, buttoninfo, bobj) {

   SocialCalc.DoCmd(bobj.element, bobj.functionobj.command);

   }

//
// SocialCalc.DoCmd(obj, which)
//
// xxx
//

SocialCalc.DoCmd = function(obj, which) {
   var combostr, sstr, cl, i, clele, slist, slistele, str, sele, rele, lele, ele, sortrange, nrange, rparts;
   var cell, color, bgcolor, defaultcolor, defaultbgcolor, sel, cmd, idp, main, vp;

   var spreadsheet = SocialCalc.GetSpreadsheetControlObject(obj.getAttribute('data-spreadsheet'));
   var editor = spreadsheet.editor;
   var sheet = spreadsheet.sheet;
   var scf = SocialCalc.Formula;
   var scc = SocialCalc.Constants;
   var fcl = scc.function_classlist;

   switch (which) {
      case "undo":
         spreadsheet.ExecuteCommand("undo", "");
         break;

      case "redo":
         spreadsheet.ExecuteCommand("redo", "");
         break;

      case "fill-rowcolstuff":
      case "fill-text":
         cl = which.substring(5);
         clele = document.getElementById(spreadsheet.idPrefix+cl+"list");
         clele.length = 0;
         for (i=0; i<SocialCalc.SpreadsheetCmdTable[cl].length; i++) {
            clele.options[i] = new Option(SocialCalc.SpreadsheetCmdTable[cl][i].t);
            }
         which = "changed-"+cl; // fall through to changed code

      case "changed-rowcolstuff":
      case "changed-text":
         cl = which.substring(8);
         clele = document.getElementById(spreadsheet.idPrefix+cl+"list");
         slist = SocialCalc.SpreadsheetCmdTable.slists[SocialCalc.SpreadsheetCmdTable[cl][clele.selectedIndex].s]; // get sList for this command
         slistele = document.getElementById(spreadsheet.idPrefix+cl+"slist");
         slistele.length = 0; // reset
         for (i=0; i<(slist.length||0); i++) {
            slistele.options[i] = new Option(slist[i].t, slist[i].s);
            }
         return; // nothing else to do

      case "ok-rowcolstuff":
      case "ok-text":
         cl = which.substring(3);
         clele = document.getElementById(spreadsheet.idPrefix+cl+"list");
         slistele = document.getElementById(spreadsheet.idPrefix+cl+"slist");
         combostr = SocialCalc.SpreadsheetCmdTable[cl][clele.selectedIndex].c;
         sstr = slistele[slistele.selectedIndex].value;
         SocialCalc.SpreadsheetControlExecuteCommand(obj, combostr, sstr);
         break;

      case "ok-setsort":
         lele = document.getElementById(spreadsheet.idPrefix+"sortlist");
         if (lele.selectedIndex==0) {
            if (editor.range.hasrange) {
               spreadsheet.sortrange = SocialCalc.crToCoord(editor.range.left, editor.range.top) + ":" +
                          SocialCalc.crToCoord(editor.range.right, editor.range.bottom);
               }
            else {
               spreadsheet.sortrange = editor.ecell.coord+":"+editor.ecell.coord;
               }
            }
         else {
            spreadsheet.sortrange = lele.options[lele.selectedIndex].value;
            }
         ele = document.getElementById(spreadsheet.idPrefix+"sortbutton");
         ele.value = SocialCalc.LocalizeString("Sort ")+spreadsheet.sortrange;
         ele.style.visibility = "visible";
         SocialCalc.LoadColumnChoosers(spreadsheet);
         if (obj && obj.blur) obj.blur();
         SocialCalc.KeyboardFocus();   
         return;

      case "dosort":
         if (spreadsheet.sortrange && spreadsheet.sortrange.indexOf(":")==-1) { // sortrange is a named range
            nrange = SocialCalc.Formula.LookupName(spreadsheet.sheet, spreadsheet.sortrange || "");
            if (nrange.type != "range") return;
            rparts = nrange.value.match(/^(.*)\|(.*)\|$/);
            sortrange = rparts[1] + ":" + rparts[2];
            }
         else {
            sortrange = spreadsheet.sortrange;
            }
         if (sortrange == "A1:A1") return;
         str = "sort "+sortrange+" ";
         sele = document.getElementById(spreadsheet.idPrefix+"majorsort");
         rele = document.getElementById(spreadsheet.idPrefix+"majorsortup");
         str += sele.options[sele.selectedIndex].value + (rele.checked ? " up" : " down");
         sele = document.getElementById(spreadsheet.idPrefix+"minorsort");
         if (sele.selectedIndex>0) {
           rele = document.getElementById(spreadsheet.idPrefix+"minorsortup");
           str += " "+sele.options[sele.selectedIndex].value + (rele.checked ? " up" : " down");
           }
         sele = document.getElementById(spreadsheet.idPrefix+"lastsort");
         if (sele.selectedIndex>0) {
           rele = document.getElementById(spreadsheet.idPrefix+"lastsortup");
           str += " "+sele.options[sele.selectedIndex].value + (rele.checked ? " up" : " down");
           }
         spreadsheet.ExecuteCommand(str, "");
         break;

      case "merge":
         combostr = SocialCalc.SpreadsheetCmdLookup[which] || "";
         sstr = SocialCalc.SpreadsheetCmdSLookup[which] || "";
         spreadsheet.ExecuteCommand(combostr, sstr);
         if (editor.range.hasrange) { // set ecell to upper left
            editor.MoveECell(SocialCalc.crToCoord(editor.range.left, editor.range.top));
            editor.RangeRemove();
            }
         break;

      case "movefrom":
         if (editor.range2.hasrange) { // toggle if already there
            spreadsheet.context.cursorsuffix = "";
            editor.Range2Remove();
            spreadsheet.ExecuteCommand("redisplay", "");
            }
         else if (editor.range.hasrange) { // set range2 to range or one cell
            editor.range2.top = editor.range.top;
            editor.range2.right = editor.range.right;
            editor.range2.bottom = editor.range.bottom;
            editor.range2.left = editor.range.left;
            editor.range2.hasrange = true;
            editor.MoveECell(SocialCalc.crToCoord(editor.range.left, editor.range.top));
            }
         else {
            editor.range2.top = editor.ecell.row;
            editor.range2.right = editor.ecell.col;
            editor.range2.bottom = editor.ecell.row;
            editor.range2.left = editor.ecell.col;
            editor.range2.hasrange = true;
            }
         str = editor.range2.hasrange ? "" : "off";
         ele = document.getElementById(spreadsheet.idPrefix+"button_movefrom");
         ele.src=spreadsheet.imagePrefix+"movefrom"+str+".gif";
         ele = document.getElementById(spreadsheet.idPrefix+"button_movepaste");
         ele.src=spreadsheet.imagePrefix+"movepaste"+str+".gif";
         ele = document.getElementById(spreadsheet.idPrefix+"button_moveinsert");
         ele.src=spreadsheet.imagePrefix+"moveinsert"+str+".gif";
         if (editor.range2.hasrange) editor.RangeRemove();
         break;

      case "movepaste":
      case "moveinsert":
         if (editor.range2.hasrange) {
            spreadsheet.context.cursorsuffix = "";
            combostr = which+" "+
               SocialCalc.crToCoord(editor.range2.left, editor.range2.top) + ":" +
               SocialCalc.crToCoord(editor.range2.right, editor.range2.bottom)
               +" "+editor.ecell.coord;
            spreadsheet.ExecuteCommand(combostr, "");
            editor.Range2Remove();
            ele = document.getElementById(spreadsheet.idPrefix+"button_movefrom");
            ele.src=spreadsheet.imagePrefix+"movefromoff.gif";
            ele = document.getElementById(spreadsheet.idPrefix+"button_movepaste");
            ele.src=spreadsheet.imagePrefix+"movepasteoff.gif";
            ele = document.getElementById(spreadsheet.idPrefix+"button_moveinsert");
            ele.src=spreadsheet.imagePrefix+"moveinsertoff.gif";
            }
         break;

      case "swapcolors":
         cell = sheet.GetAssuredCell(editor.ecell.coord);
         defaultcolor = sheet.attribs.defaultcolor ? sheet.colors[sheet.attribs.defaultcolor] : "rgb(0,0,0)";
         defaultbgcolor = sheet.attribs.defaultbgcolor ? sheet.colors[sheet.attribs.defaultbgcolor] : "rgb(255,255,255)";
         color = cell.color ? sheet.colors[cell.color] : defaultcolor; // get color
         if (color == defaultbgcolor) color = ""; // going to swap, so if same as background default, use default
         bgcolor = cell.bgcolor ? sheet.colors[cell.bgcolor] : defaultbgcolor;
         if (bgcolor == defaultcolor) bgcolor = ""; // going to swap, so if same as foreground default, use default
         spreadsheet.ExecuteCommand("set %C color "+bgcolor+"%Nset %C bgcolor "+color, "");
         break;

      case "sum":
          if (editor.range.hasrange) {
              sel = SocialCalc.crToCoord(editor.range.left, editor.range.top)+
                  ":"+SocialCalc.crToCoord(editor.range.right, editor.range.bottom);
              cmd = "set "+SocialCalc.crToCoord(editor.range.right, editor.range.bottom+1)+
                  " formula sum("+sel+")";
          }
          else {
              row = editor.ecell.row - 1;
              col = editor.ecell.col;
              if (row<=1) {
                  cmd = "set "+editor.ecell.coord+" constant e#REF! 0 #REF!";
              }
              else {
                  foundvalue = false;
                  while (row>0) {
                      cr = SocialCalc.crToCoord(col, row);
                      cell = sheet.GetAssuredCell(cr);
                      if (!cell.datatype || cell.datatype=="t") {
                          if (foundvalue) {
                              row++;
                              break;
                          }
                      }
                      else {
                          foundvalue = true;
                      }
                      row--;
                  }
                  cmd = "set "+editor.ecell.coord+" formula sum("+
                      SocialCalc.crToCoord(col,row)+":"+SocialCalc.crToCoord(col, editor.ecell.row-1)+")";
              }
          }

          editor.EditorScheduleSheetCommands(cmd, true, false);
          break;

      case "function-list":
          idp = spreadsheet.idPrefix+"function";
          ele = document.getElementById(idp+"dialog");
          if (ele) return; // already have one

          scf.FillFunctionInfo();

          str = '<table><tr><td><span style="font-size:x-small;font-weight:bold">%loc!Category!</span><br>'+
              '<select id="'+idp+'class" size="'+fcl.length+'" style="width:120px;" onchange="SocialCalc.SpreadsheetControl.FunctionClassChosen(this.options[this.selectedIndex].value, \'' +
              spreadsheet.idPrefix + '\');">';
          for (i=0; i<fcl.length; i++) {
              str += '<option value="'+fcl[i]+'"'+(i==0?' selected>':'>')+SocialCalc.special_chars(scf.FunctionClasses[fcl[i]].name)+'</option>';
          }
          str += '</select></td><td>&nbsp;&nbsp;</td><td id="'+idp+'list"><span style="font-size:x-small;font-weight:bold">%loc!Functions!</span><br>'+
              '<select id="'+idp+'name" size="'+fcl.length+'" style="width:240px;" '+
              'onchange="SocialCalc.SpreadsheetControl.FunctionChosen(this.options[this.selectedIndex].value, \'' + spreadsheet.idPrefix +
              '\');" ondblclick="SocialCalc.SpreadsheetControl.DoFunctionPaste(\'' + spreadsheet.idPrefix + '\');">';
          str += SocialCalc.SpreadsheetControl.GetFunctionNamesStr("all");
          str += '</td></tr><tr><td colspan="3">'+
              '<div id="'+idp+'desc" style="width:380px;height:80px;overflow:auto;font-size:x-small;">'+SocialCalc.SpreadsheetControl.GetFunctionInfoStr(scf.FunctionClasses[fcl[0]].items[0])+'</div>'+
              '<div style="width:380px;text-align:right;padding-top:6px;font-size:small;">'+
              '<input type="button" value="%loc!Paste!" style="font-size:smaller;" onclick="SocialCalc.SpreadsheetControl.DoFunctionPaste(\'' + spreadsheet.idPrefix + '\');">&nbsp;'+
              '<input type="button" value="%loc!Cancel!" style="font-size:smaller;" onclick="SocialCalc.SpreadsheetControl.HideFunctions(\'' + spreadsheet.idPrefix + '\');"></div>'+
              '</td></tr></table>';

          main = document.createElement("div");
          main.id = idp+"dialog";

          main.style.position = "absolute";

          vp = SocialCalc.GetViewportInfo();

          main.style.top = (vp.height/3)+"px";
          main.style.left = (vp.width/3)+"px";
          main.style.zIndex = 100;
          main.style.backgroundColor = "#FFF";
          main.style.border = "1px solid black";

          main.style.width = "400px";

          str = '<table cellspacing="0" cellpadding="0" style="border-bottom:1px solid black;"><tr>'+
              '<td style="font-size:10px;cursor:default;width:100%;background-color:#999;color:#FFF;">'+"&nbsp;%loc!Function List!"+'</td>'+
              '<td style="font-size:10px;cursor:default;color:#666;" onclick="SocialCalc.SpreadsheetControl.HideFunctions(\'' + spreadsheet.idPrefix + '\');">&nbsp;X&nbsp;</td></tr></table>'+
              '<div style="background-color:#DDD;">'+str+'</div>';

          str = SocialCalc.LocalizeSubstrings(str);

          main.innerHTML = str;

          SocialCalc.DragRegister(main.firstChild.firstChild.firstChild.firstChild, true, true, {MouseDown: SocialCalc.DragFunctionStart, MouseMove: SocialCalc.DragFunctionPosition,
              MouseUp: SocialCalc.DragFunctionPosition,
              Disabled: null, positionobj: main});

          spreadsheet.spreadsheetDiv.appendChild(main);

          ele = document.getElementById(idp+"name");
          ele.focus();
          SocialCalc.CmdGotFocus(ele);
          break;

      default:
         combostr = SocialCalc.SpreadsheetCmdLookup[which] || "";
         sstr = SocialCalc.SpreadsheetCmdSLookup[which] || "";
         spreadsheet.ExecuteCommand(combostr, sstr);
         break;
      }

   if (obj && obj.blur) obj.blur();
   SocialCalc.KeyboardFocus();

   }

SocialCalc.SpreadsheetCmdLookup = {
    'copy': 'copy %C all',
    'cut': 'cut %C all',
    'paste': 'paste %C all',
    'pasteformats': 'paste %C formats',
    'delete': 'erase %C formulas',
    'filldown': 'filldown %C all',
    'fillright': 'fillright %C all',
    'erase': 'erase %C all',
    'borderon': 'set %C bt %S%Nset %C br %S%Nset %C bb %S%Nset %C bl %S',
    'borderoff': 'set %C bt %S%Nset %C br %S%Nset %C bb %S%Nset %C bl %S',
    'merge': 'merge %C',
    'unmerge': 'unmerge %C',
    'align-left': 'set %C cellformat left',
    'align-center': 'set %C cellformat center',
    'align-right': 'set %C cellformat right',
    'align-default': 'set %C cellformat',
    'insertrow': 'insertrow %C',
    'insertcol': 'insertcol %C',
    'deleterow': 'deleterow %C',
    'deletecol': 'deletecol %C',
    'undo': 'undo',
    'redo': 'redo',
    'recalc': 'recalc',
    'percent': 'set %C nontextvalueformat #,##0.00%'
};

SocialCalc.SpreadsheetCmdSLookup = {
    'borderon': '1px solid rgb(0,0,0)',
    'borderoff': ''
};

//
// SocialCalc.SpreadsheetControlExecuteCommand(obj, combostr, sstr)
//
// xxx
//

SocialCalc.SpreadsheetControlExecuteCommand = function(obj, combostr, sstr) {

   var i, commands;
   var spreadsheet = obj;
   var eobj = spreadsheet.editor;

   var str = {};
   str.P = "%";
   str.N = "\n"
   if (eobj.range.hasrange) {
      str.R = SocialCalc.crToCoord(eobj.range.left, eobj.range.top)+
             ":"+SocialCalc.crToCoord(eobj.range.right, eobj.range.bottom);
      str.C = str.R;
      str.W = SocialCalc.rcColname(eobj.range.left) + ":" + SocialCalc.rcColname(eobj.range.right);
      }
   else {
      str.C = eobj.ecell.coord;
      str.R = eobj.ecell.coord+":"+eobj.ecell.coord;
      str.W = SocialCalc.rcColname(SocialCalc.coordToCr(eobj.ecell.coord).col);
      }
   str.S = sstr;
   combostr = combostr.replace(/%C/g, str.C);
   combostr = combostr.replace(/%R/g, str.R);
   combostr = combostr.replace(/%N/g, str.N);
   combostr = combostr.replace(/%S/g, str.S);
   combostr = combostr.replace(/%W/g, str.W);
   combostr = combostr.replace(/%P/g, str.P);

   eobj.EditorScheduleSheetCommands(combostr, true, false);

   }

//
// result = SocialCalc.SpreadsheetControlCreateSheetHTML(spreadsheet)
//
// Returns the HTML representation of the whole spreadsheet
//

SocialCalc.SpreadsheetControlCreateSheetHTML = function(spreadsheet) {

   var context, div, ele;

   var result = "";

   context = new SocialCalc.RenderContext(spreadsheet.sheet);
   div = document.createElement("div");
   ele = context.RenderSheet(null, {type: "html"});
   div.appendChild(ele);
   delete context;
   result = div.innerHTML;
   delete ele;
   delete div;
   return result;

   }

//
// result = SocialCalc.SpreadsheetControlCreateCellHTML(spreadsheet, coord, linkstyle)
//
// Returns the HTML representation of a cell. Blank is "", not "&nbsp;".
//

SocialCalc.SpreadsheetControlCreateCellHTML = function(spreadsheet, coord, linkstyle) {

   var result = "";
   var cell = spreadsheet.sheet.cells[coord];

   if (!cell) return "";

   if (cell.displaystring == undefined) {
      result = SocialCalc.FormatValueForDisplay(spreadsheet.sheet, cell.datavalue, coord, (linkstyle || spreadsheet.context.defaultHTMLlinkstyle));
      }
   else {
      result = cell.displaystring;
      }

   if (result == "&nbsp;") result = "";

   return result;

   }

//
// result = SocialCalc.SpreadsheetControlCreateCellHTMLSave(spreadsheet, range, linkstyle)
//
// Returns the HTML representation of a range of cells, or the whole sheet if range is null.
// The form is:
//    version:1.0
//    coord:cell-HTML
//    coord:cell-HTML
//    ...
//
// Empty cells are skipped. The cell-HTML is encoded with ":"=>"\c", newline=>"\n", and "\"=>"\b".
//

SocialCalc.SpreadsheetControlCreateCellHTMLSave = function(spreadsheet, range, linkstyle) {

   var cr1, cr2, row, col, coord, cell, cellHTML;
   var result = [];
   var prange;

   if (range) {
      prange = SocialCalc.ParseRange(range);
      }
   else {
      prange = {cr1: {row: 1, col:1},
                cr2: {row: spreadsheet.sheet.attribs.lastrow, col: spreadsheet.sheet.attribs.lastcol}};
      }
   cr1 = prange.cr1;
   cr2 = prange.cr2;

   result.push("version:1.0");

   for (row=cr1.row; row <= cr2.row; row++) {
      for (col=cr1.col; col <= cr2.col; col++) {
         coord = SocialCalc.crToCoord(col, row);
         cell=spreadsheet.sheet.cells[coord];
         if (!cell) continue;
         if (cell.displaystring == undefined) {
            cellHTML = SocialCalc.FormatValueForDisplay(spreadsheet.sheet, cell.datavalue, coord, (linkstyle || spreadsheet.context.defaultHTMLlinkstyle));
            }
         else {
            cellHTML = cell.displaystring;
            }
         if (cellHTML == "&nbsp;") continue;
         result.push(coord+":"+SocialCalc.encodeForSave(cellHTML));
         }
      }

   result.push(""); // one extra to get extra \n
   return result.join("\n");
   }

//
// Formula Bar Button Routines
//

SocialCalc.SpreadsheetControl.DoFunctionList = function() {

   var i, cname, str, f, ele;

   var scf = SocialCalc.Formula;
   var scc = SocialCalc.Constants;
   var fcl = scc.function_classlist;

   var spreadsheet = SocialCalc.GetSpreadsheetControlObject();
   var idp = spreadsheet.idPrefix+"function";

   ele = document.getElementById(idp+"dialog");
   if (ele) return; // already have one

   scf.FillFunctionInfo();

   str = '<table><tr><td><span style="font-size:x-small;font-weight:bold">%loc!Category!</span><br>'+
      '<select id="'+idp+'class" size="'+fcl.length+'" style="width:120px;" onchange="SocialCalc.SpreadsheetControl.FunctionClassChosen(this.options[this.selectedIndex].value);">';
   for (i=0; i<fcl.length; i++) {
      str += '<option value="'+fcl[i]+'"'+(i==0?' selected>':'>')+SocialCalc.special_chars(scf.FunctionClasses[fcl[i]].name)+'</option>';
      }
   str += '</select></td><td>&nbsp;&nbsp;</td><td id="'+idp+'list"><span style="font-size:x-small;font-weight:bold">%loc!Functions!</span><br>'+
      '<select id="'+idp+'name" size="'+fcl.length+'" style="width:240px;" '+
      'onchange="SocialCalc.SpreadsheetControl.FunctionChosen(this.options[this.selectedIndex].value);" ondblclick="SocialCalc.SpreadsheetControl.DoFunctionPaste();">';
   str += SocialCalc.SpreadsheetControl.GetFunctionNamesStr("all");
   str += '</td></tr><tr><td colspan="3">'+
          '<div id="'+idp+'desc" style="width:380px;height:80px;overflow:auto;font-size:x-small;">'+SocialCalc.SpreadsheetControl.GetFunctionInfoStr(scf.FunctionClasses[fcl[0]].items[0])+'</div>'+
          '<div style="width:380px;text-align:right;padding-top:6px;font-size:small;">'+
          '<input type="button" value="%loc!Paste!" style="font-size:smaller;" onclick="SocialCalc.SpreadsheetControl.DoFunctionPaste();">&nbsp;'+
          '<input type="button" value="%loc!Cancel!" style="font-size:smaller;" onclick="SocialCalc.SpreadsheetControl.HideFunctions();"></div>'+
          '</td></tr></table>';

   var main = document.createElement("div");
   main.id = idp+"dialog";

   main.style.position = "absolute";

   var vp = SocialCalc.GetViewportInfo();

   main.style.top = (vp.height/3)+"px";
   main.style.left = (vp.width/3)+"px";
   main.style.zIndex = 100;
   main.style.backgroundColor = "#FFF";
   main.style.border = "1px solid black";

   main.style.width = "400px";

   str = '<table cellspacing="0" cellpadding="0" style="border-bottom:1px solid black;"><tr>'+
      '<td style="font-size:10px;cursor:default;width:100%;background-color:#999;color:#FFF;">'+"&nbsp;%loc!Function List!"+'</td>'+
      '<td style="font-size:10px;cursor:default;color:#666;" onclick="SocialCalc.SpreadsheetControl.HideFunctions();">&nbsp;X&nbsp;</td></tr></table>'+
      '<div style="background-color:#DDD;">'+str+'</div>';

   str = SocialCalc.LocalizeSubstrings(str);

   main.innerHTML = str;

   SocialCalc.DragRegister(main.firstChild.firstChild.firstChild.firstChild, true, true, {MouseDown: SocialCalc.DragFunctionStart, MouseMove: SocialCalc.DragFunctionPosition,
                  MouseUp: SocialCalc.DragFunctionPosition,
                  Disabled: null, positionobj: main});

   spreadsheet.spreadsheetDiv.appendChild(main);

   ele = document.getElementById(idp+"name");
   ele.focus();
   SocialCalc.CmdGotFocus(ele);
//!!! need to do keyboard handling: if esc, hide; if All, letter scrolls to there

   }

SocialCalc.SpreadsheetControl.GetFunctionNamesStr = function(cname) {

   var i, f;
   var scf = SocialCalc.Formula;
   var str = "";

   f = scf.FunctionClasses[cname];
   for (i=0; i<f.items.length; i++) {
      str += '<option value="'+f.items[i]+'"'+(i==0?' selected>':'>')+f.items[i]+'</option>';
      }

   return str;

   }

SocialCalc.SpreadsheetControl.FillFunctionNames = function(cname, ele) {

   var i, f;
   var scf = SocialCalc.Formula;

   ele.length = 0;
   f = scf.FunctionClasses[cname];
   for (i=0; i<f.items.length; i++) {
      ele.options[i] = new Option(f.items[i], f.items[i]);
      if (i==0) {
         ele.options[i].selected = true;
         }
      }
   }

SocialCalc.SpreadsheetControl.GetFunctionInfoStr = function(fname) {
   
   var scf = SocialCalc.Formula;
   var f = scf.FunctionList[fname];
   var scsc = SocialCalc.special_chars;

   var str = "<b>"+fname+"("+scsc(scf.FunctionArgString(fname))+")</b><br>";
   str += scsc(f[3]);

   return str;

   }

SocialCalc.SpreadsheetControl.FunctionClassChosen = function(cname, idPrefix) {

   var spreadsheet = SocialCalc.GetSpreadsheetControlObject(idPrefix);
   var idp = spreadsheet.idPrefix+"function";
   var scf = SocialCalc.Formula;

   SocialCalc.SpreadsheetControl.FillFunctionNames(cname, document.getElementById(idp+"name"));

   SocialCalc.SpreadsheetControl.FunctionChosen(scf.FunctionClasses[cname].items[0]);

   }

SocialCalc.SpreadsheetControl.FunctionChosen = function(fname, idPrefix) {

   var spreadsheet = SocialCalc.GetSpreadsheetControlObject(idPrefix);
   var idp = spreadsheet.idPrefix+"function";

   document.getElementById(idp+"desc").innerHTML = SocialCalc.SpreadsheetControl.GetFunctionInfoStr(fname);

   }

SocialCalc.SpreadsheetControl.HideFunctions = function(idPrefix) {

   var spreadsheet = SocialCalc.GetSpreadsheetControlObject(idPrefix);

   var ele = document.getElementById(spreadsheet.idPrefix+"functiondialog");
   ele.innerHTML = "";

   SocialCalc.DragUnregister(ele);

   SocialCalc.KeyboardFocus();

   if (ele.parentNode) {
      ele.parentNode.removeChild(ele);
      }

   }

SocialCalc.SpreadsheetControl.DoFunctionPaste = function(idPrefix) {

   var spreadsheet = SocialCalc.GetSpreadsheetControlObject(idPrefix);
   var editor = spreadsheet.editor;
   var ele = document.getElementById(spreadsheet.idPrefix+"functionname");
   var mele = document.getElementById(spreadsheet.idPrefix+"multilinetextarea");

   var text = ele.value+"(";

   SocialCalc.SpreadsheetControl.HideFunctions(idPrefix);

   if (mele) { // multi-line editing is in progress
      mele.value += text;
      mele.focus();
      SocialCalc.CmdGotFocus(mele);
      }
   else {
      editor.EditorAddToInput(text, "=");
      }

   }


SocialCalc.SpreadsheetControl.DoMultiline = function() {

   var SCLocSS = SocialCalc.LocalizeSubstrings;

   var str, ele, text;

   var scc = SocialCalc.Constants;
   var spreadsheet = SocialCalc.GetSpreadsheetControlObject();
   var editor = spreadsheet.editor;
   var wval = editor.workingvalues;

   var spreadsheet = SocialCalc.GetSpreadsheetControlObject();
   var idp = spreadsheet.idPrefix+"multiline";

   ele = document.getElementById(idp+"dialog");
   if (ele) return; // already have one

   switch (editor.state) {
      case "start":
         wval.ecoord = editor.ecell.coord;
         wval.erow = editor.ecell.row;
         wval.ecol = editor.ecell.col;
         editor.RangeRemove();
         text = SocialCalc.GetCellContents(editor.context.sheetobj, wval.ecoord);
         break;

      case "input":
      case "inputboxdirect":
         text = editor.inputBox.GetText();
         break;
      }

   editor.inputBox.element.disabled = true;

   text = SocialCalc.special_chars(text);

   str = '<textarea id="'+idp+'textarea" style="width:380px;height:120px;margin:10px 0px 0px 6px;">'+text+'</textarea>'+
         '<div style="width:380px;text-align:right;padding:6px 0px 4px 6px;font-size:small;">'+
         SCLocSS('<input type="button" value="%loc!Set Cell Contents!" style="font-size:smaller;" onclick="SocialCalc.SpreadsheetControl.DoMultilinePaste();">&nbsp;'+
         '<input type="button" value="%loc!Clear!" style="font-size:smaller;" onclick="SocialCalc.SpreadsheetControl.DoMultilineClear();">&nbsp;'+
         '<input type="button" value="%loc!Cancel!" style="font-size:smaller;" onclick="SocialCalc.SpreadsheetControl.HideMultiline();"></div>'+
         '</div>');

   var main = document.createElement("div");
   main.id = idp+"dialog";

   main.style.position = "absolute";

   var vp = SocialCalc.GetViewportInfo();

   main.style.top = (vp.height/3)+"px";
   main.style.left = (vp.width/3)+"px";
   main.style.zIndex = 100;
   main.style.backgroundColor = "#FFF";
   main.style.border = "1px solid black";

   main.style.width = "400px";

   main.innerHTML = '<table cellspacing="0" cellpadding="0" style="border-bottom:1px solid black;"><tr>'+
      '<td style="font-size:10px;cursor:default;width:100%;background-color:#999;color:#FFF;">'+
      SCLocSS("&nbsp;%loc!Multi-line Input Box!")+'</td>'+
      '<td style="font-size:10px;cursor:default;color:#666;" onclick="SocialCalc.SpreadsheetControl.HideMultiline();">&nbsp;X&nbsp;</td></tr></table>'+
      '<div style="background-color:#DDD;">'+str+'</div>';

   SocialCalc.DragRegister(main.firstChild.firstChild.firstChild.firstChild, true, true, {MouseDown: SocialCalc.DragFunctionStart, MouseMove: SocialCalc.DragFunctionPosition,
                  MouseUp: SocialCalc.DragFunctionPosition,
                  Disabled: null, positionobj: main});

   spreadsheet.spreadsheetDiv.appendChild(main);

   ele = document.getElementById(idp+"textarea");
   ele.focus();
   SocialCalc.CmdGotFocus(ele);
//!!! need to do keyboard handling: if esc, hide?

   }


SocialCalc.SpreadsheetControl.HideMultiline = function() {

   var scc = SocialCalc.Constants;
   var spreadsheet = SocialCalc.GetSpreadsheetControlObject();
   var editor = spreadsheet.editor;

   var ele = document.getElementById(spreadsheet.idPrefix+"multilinedialog");
   ele.innerHTML = "";

   SocialCalc.DragUnregister(ele);

   SocialCalc.KeyboardFocus();

   if (ele.parentNode) {
      ele.parentNode.removeChild(ele);
      }

   switch (editor.state) {
      case "start":
         editor.inputBox.DisplayCellContents(null);
         break;

      case "input":
      case "inputboxdirect":
         editor.inputBox.element.disabled = false;
         editor.inputBox.Focus();
         break;
      }

   }

SocialCalc.SpreadsheetControl.DoMultilineClear = function() {

   var spreadsheet = SocialCalc.GetSpreadsheetControlObject();

   var ele = document.getElementById(spreadsheet.idPrefix+"multilinetextarea");

   ele.value = "";
   ele.focus();

   }


SocialCalc.SpreadsheetControl.DoMultilinePaste = function() {

   var spreadsheet = SocialCalc.GetSpreadsheetControlObject();
   var editor = spreadsheet.editor;
   var wval = editor.workingvalues;

   var ele = document.getElementById(spreadsheet.idPrefix+"multilinetextarea");

   var text = ele.value;

   SocialCalc.SpreadsheetControl.HideMultiline();

   switch (editor.state) {
      case "start":
         wval.partialexpr = "";
         wval.ecoord = editor.ecell.coord;
         wval.erow = editor.ecell.row;
         wval.ecol = editor.ecell.col;
         break;
      case "input":
      case "inputboxdirect":
         editor.inputBox.Blur();
         editor.inputBox.ShowInputBox(false);
         editor.state = "start";
         break;
      }

   editor.EditorSaveEdit(text);

   }


SocialCalc.SpreadsheetControl.DoLink = function() {

   var SCLoc = SocialCalc.LocalizeString;

   var str, ele, text, cell, setformat, popup;

   var scc = SocialCalc.Constants;
   var spreadsheet = SocialCalc.GetSpreadsheetControlObject();
   var editor = spreadsheet.editor;
   var wval = editor.workingvalues;

   var spreadsheet = SocialCalc.GetSpreadsheetControlObject();
   var idp = spreadsheet.idPrefix+"link";

   ele = document.getElementById(idp+"dialog");
   if (ele) return; // already have one

   switch (editor.state) {
      case "start":
         wval.ecoord = editor.ecell.coord;
         wval.erow = editor.ecell.row;
         wval.ecol = editor.ecell.col;
         editor.RangeRemove();
         text = SocialCalc.GetCellContents(editor.context.sheetobj, wval.ecoord);
         break;

      case "input":
      case "inputboxdirect":
         text = editor.inputBox.GetText();
         break;
      }

   editor.inputBox.element.disabled = true;

   if (text.charAt(0)=="'") {
      text = text.slice(1);
      }

   var parts = SocialCalc.ParseCellLinkText(text);

   text = SocialCalc.special_chars(text);

   cell = spreadsheet.sheet.cells[editor.ecell.coord];
   if (!cell || !cell.textvalueformat) { // set to link format, but don't override
      setformat = " checked";
      }
   else {
      setformat = "";
      }

   popup = parts.newwin ? " checked" : "";

   str = '<div style="padding:6px 0px 4px 6px;">'+
         '<span style="font-size:smaller;">'+SCLoc("Description")+'</span><br>'+
         '<input type="text" id="'+idp+'desc" style="width:380px;" value="'+SocialCalc.special_chars(parts.desc)+'"><br>'+
         '<span style="font-size:smaller;">'+SCLoc("URL")+'</span><br>'+
         '<input type="text" id="'+idp+'url" style="width:380px;" value="'+SocialCalc.special_chars(parts.url)+'"><br>';
   if (SocialCalc.Callbacks.MakePageLink) { // only show if handling pagenames here
      str += '<span style="font-size:smaller;">'+SCLoc("Page Name")+'</span><br>'+
             '<input type="text" id="'+idp+'pagename" style="width:380px;" value="'+SocialCalc.special_chars(parts.pagename)+'"><br>'+
             '<span style="font-size:smaller;">'+SCLoc("Workspace")+'</span><br>'+
             '<input type="text" id="'+idp+'workspace" style="width:380px;" value="'+SocialCalc.special_chars(parts.workspace)+'"><br>';
      }
   str += SocialCalc.LocalizeSubstrings('<input type="checkbox" id="'+idp+'format"'+setformat+'>&nbsp;'+
         '<span style="font-size:smaller;">%loc!Set to Link format!</span><br>'+
         '<input type="checkbox" id="'+idp+'popup"'+popup+'>&nbsp;'+
         '<span style="font-size:smaller;">%loc!Show in new browser window!</span>'+
         '</div>'+
         '<div style="width:380px;text-align:right;padding:6px 0px 4px 6px;font-size:small;">'+
         '<input type="button" value="%loc!Set Cell Contents!" style="font-size:smaller;" onclick="SocialCalc.SpreadsheetControl.DoLinkPaste();">&nbsp;'+
         '<input type="button" value="%loc!Clear!" style="font-size:smaller;" onclick="SocialCalc.SpreadsheetControl.DoLinkClear();">&nbsp;'+
         '<input type="button" value="%loc!Cancel!" style="font-size:smaller;" onclick="SocialCalc.SpreadsheetControl.HideLink();"></div>'+
         '</div>');

   var main = document.createElement("div");
   main.id = idp+"dialog";

   main.style.position = "absolute";

   var vp = SocialCalc.GetViewportInfo();

   main.style.top = (vp.height/3)+"px";
   main.style.left = (vp.width/3)+"px";
   main.style.zIndex = 100;
   main.style.backgroundColor = "#FFF";
   main.style.border = "1px solid black";

   main.style.width = "400px";

   main.innerHTML = '<table cellspacing="0" cellpadding="0" style="border-bottom:1px solid black;"><tr>'+
      '<td style="font-size:10px;cursor:default;width:100%;background-color:#999;color:#FFF;">'+"&nbsp;"+SCLoc("Link Input Box")+'</td>'+
      '<td style="font-size:10px;cursor:default;color:#666;" onclick="SocialCalc.SpreadsheetControl.HideLink();">&nbsp;X&nbsp;</td></tr></table>'+
      '<div style="background-color:#DDD;">'+str+'</div>';

   SocialCalc.DragRegister(main.firstChild.firstChild.firstChild.firstChild, true, true, {MouseDown: SocialCalc.DragFunctionStart, MouseMove: SocialCalc.DragFunctionPosition,
                  MouseUp: SocialCalc.DragFunctionPosition,
                  Disabled: null, positionobj: main});

   spreadsheet.spreadsheetDiv.appendChild(main);

   ele = document.getElementById(idp+"url");
   ele.focus();
   SocialCalc.CmdGotFocus(ele);
//!!! need to do keyboard handling: if esc, hide?

   }


SocialCalc.SpreadsheetControl.HideLink = function() {

   var scc = SocialCalc.Constants;
   var spreadsheet = SocialCalc.GetSpreadsheetControlObject();
   var editor = spreadsheet.editor;

   var ele = document.getElementById(spreadsheet.idPrefix+"linkdialog");
   ele.innerHTML = "";

   SocialCalc.DragUnregister(ele);

   SocialCalc.KeyboardFocus();

   if (ele.parentNode) {
      ele.parentNode.removeChild(ele);
      }

   switch (editor.state) {
      case "start":
         editor.inputBox.DisplayCellContents(null);
         break;

      case "input":
      case "inputboxdirect":
         editor.inputBox.element.disabled = false;
         editor.inputBox.Focus();
         break;
      }

   }

SocialCalc.SpreadsheetControl.DoLinkClear = function() {

   var spreadsheet = SocialCalc.GetSpreadsheetControlObject();

   document.getElementById(spreadsheet.idPrefix+"linkdesc").value = "";
   document.getElementById(spreadsheet.idPrefix+"linkpagename").value = "";
   document.getElementById(spreadsheet.idPrefix+"linkworkspace").value = "";

   var ele = document.getElementById(spreadsheet.idPrefix+"linkurl");
   ele.value = "";
   ele.focus();

   }


SocialCalc.SpreadsheetControl.DoLinkPaste = function() {

   var spreadsheet = SocialCalc.GetSpreadsheetControlObject();
   var editor = spreadsheet.editor;
   var wval = editor.workingvalues;

   var descele = document.getElementById(spreadsheet.idPrefix+"linkdesc");
   var urlele = document.getElementById(spreadsheet.idPrefix+"linkurl");
   var pagenameele = document.getElementById(spreadsheet.idPrefix+"linkpagename");
   var workspaceele = document.getElementById(spreadsheet.idPrefix+"linkworkspace");
   var formatele = document.getElementById(spreadsheet.idPrefix+"linkformat");
   var popupele = document.getElementById(spreadsheet.idPrefix+"linkpopup");

   var text = "";

   var ltsym, gtsym, obsym, cbsym;

   if (popupele.checked) {
      ltsym = "<<"; gtsym = ">>"; obsym = "[["; cbsym = "]]";
      }
   else {
      ltsym = "<"; gtsym = ">"; obsym = "["; cbsym = "]";
      }

   if (pagenameele && pagenameele.value) {
      if (workspaceele.value) {
         text = descele.value+"{"+workspaceele.value+obsym+pagenameele.value+cbsym+"}";
         }
      else {
         text = descele.value+obsym+pagenameele.value+cbsym;
         }
      }
   else {
      text = descele.value+ltsym+urlele.value+gtsym;
      }

   SocialCalc.SpreadsheetControl.HideLink();

   switch (editor.state) {
      case "start":
         wval.partialexpr = "";
         wval.ecoord = editor.ecell.coord;
         wval.erow = editor.ecell.row;
         wval.ecol = editor.ecell.col;
         break;
      case "input":
      case "inputboxdirect":
         editor.inputBox.Blur();
         editor.inputBox.ShowInputBox(false);
         editor.state = "start";
         break;
      }

   if (formatele.checked) {
      SocialCalc.SpreadsheetControlExecuteCommand(null, "set %C textvalueformat text-link", "");
      }

   editor.EditorSaveEdit(text);

   }

//
// TAB Routines
//

// Sort

SocialCalc.SpreadsheetControlSortOnclick = function(s, t) {

   var name, i;
   var namelist = [];
   var nl = document.getElementById(s.idPrefix+"sortlist");
   SocialCalc.LoadColumnChoosers(s);
   s.editor.RangeChangeCallback.sort = SocialCalc.UpdateSortRangeProposal;

   for (name in s.sheet.names) {
      namelist.push(name);
      }
   namelist.sort();
   nl.length = 0;
   nl.options[0] = new Option(SocialCalc.LocalizeString("[select range]"));
   for (i=0; i<namelist.length; i++) {
      name = namelist[i];
      nl.options[i+1] = new Option(name, name);
      if (name == s.sortrange) {
         nl.options[i+1].selected = true;
         }
      }
   if (s.sortrange == "") {
      nl.options[0].selected = true;
      }

   SocialCalc.UpdateSortRangeProposal(s.editor);
   SocialCalc.KeyboardFocus();
   return;

   }

SocialCalc.SpreadsheetControlSortSave = function(editor, setting) {
   // Format is:
   //    sort:sortrange:major:up/down:minor:up/down:last:up/down

   var spreadsheet = SocialCalc.GetSpreadsheetControlObject(editor.idPrefix);
   var str, sele, rele;

   str = "sort:"+SocialCalc.encodeForSave(spreadsheet.sortrange)+":";
   sele = document.getElementById(spreadsheet.idPrefix+"majorsort");
   rele = document.getElementById(spreadsheet.idPrefix+"majorsortup");
   str += sele.selectedIndex + (rele.checked ? ":up" : ":down");
   sele = document.getElementById(spreadsheet.idPrefix+"minorsort");
   if (sele.selectedIndex>0) {
      rele = document.getElementById(spreadsheet.idPrefix+"minorsortup");
      str += ":"+sele.selectedIndex + (rele.checked ? ":up" : ":down");
      }
   else {
      str += "::";
      }
   sele = document.getElementById(spreadsheet.idPrefix+"lastsort");
   if (sele.selectedIndex>0) {
      rele = document.getElementById(spreadsheet.idPrefix+"lastsortup");
      str += ":"+sele.selectedIndex + (rele.checked ? ":up" : ":down");
      }
    else {
      str += "::";
      }
   return str+"\n";
   }

SocialCalc.SpreadsheetControlSortLoad = function(editor, setting, line, flags) {
   var parts, ele;

   var spreadsheet = SocialCalc.GetSpreadsheetControlObject(editor.idPrefix);

   parts = line.split(":");
   spreadsheet.sortrange = SocialCalc.decodeFromSave(parts[1]);
   ele = document.getElementById(spreadsheet.idPrefix+"sortbutton");
   if (spreadsheet.sortrange) {
      ele.value = SocialCalc.LocalizeString("Sort ")+spreadsheet.sortrange;
      ele.style.visibility = "visible";
      }
   else {
      ele.style.visibility = "hidden";
      }
   SocialCalc.LoadColumnChoosers(spreadsheet);

   sele = document.getElementById(spreadsheet.idPrefix+"majorsort");
   sele.selectedIndex = parts[2]-0;
   document.getElementById(spreadsheet.idPrefix+"majorsort"+parts[3]).checked = true;
   sele = document.getElementById(spreadsheet.idPrefix+"minorsort");
   if (parts[4]) {
      sele.selectedIndex = parts[4]-0;
      document.getElementById(spreadsheet.idPrefix+"minorsort"+parts[5]).checked = true;
      }
   else {
      sele.selectedIndex = 0;
      document.getElementById(spreadsheet.idPrefix+"minorsortup").checked = true;
      }
   sele = document.getElementById(spreadsheet.idPrefix+"lastsort");
   if (parts[6]) {
      sele.selectedIndex = parts[6]-0;
      document.getElementById(spreadsheet.idPrefix+"lastsort"+parts[7]).checked = true;
      }
    else {
      sele.selectedIndex = 0;
      document.getElementById(spreadsheet.idPrefix+"lastsortup").checked = true;
      }

   return true;
   }

// Comment

SocialCalc.SpreadsheetControlCommentOnclick = function(s, t) {
   s.editor.MoveECellCallback.comment = SocialCalc.SpreadsheetControlCommentMoveECell;
   SocialCalc.SpreadsheetControlCommentDisplay(s, t);
   SocialCalc.KeyboardFocus();
   return;
   }

SocialCalc.SpreadsheetControlCommentDisplay = function(s, t) {
   var c = "";
   if (s.editor.ecell && s.editor.ecell.coord && s.sheet.cells[s.editor.ecell.coord]) {
      c = s.sheet.cells[s.editor.ecell.coord].comment || "";
      }
   document.getElementById(s.idPrefix+"commenttext").value = c;
   }

SocialCalc.SpreadsheetControlCommentMoveECell = function(editor) {
   SocialCalc.SpreadsheetControlCommentDisplay(SocialCalc.GetSpreadsheetControlObject(), "comment");
   }

SocialCalc.SpreadsheetControlCommentSet = function() {
   var s=SocialCalc.GetSpreadsheetControlObject();
   s.ExecuteCommand("set %C comment "+SocialCalc.encodeForSave(document.getElementById(s.idPrefix+"commenttext").value));
   var cell=SocialCalc.GetEditorCellElement(s.editor, s.editor.ecell.row, s.editor.ecell.col);
   s.editor.UpdateCellCSS(cell, s.editor.ecell.row, s.editor.ecell.col);
   SocialCalc.KeyboardFocus();
   }

SocialCalc.SpreadsheetControlCommentOnunclick = function(s, t) {
   delete s.editor.MoveECellCallback.comment;
   }

// Names

SocialCalc.SpreadsheetControlNamesOnclick = function(s, t) {
   document.getElementById(s.idPrefix+"namesname").value = "";
   document.getElementById(s.idPrefix+"namesdesc").value = "";
   document.getElementById(s.idPrefix+"namesvalue").value = "";
   s.editor.RangeChangeCallback.names = SocialCalc.SpreadsheetControlNamesRangeChange;
   s.editor.MoveECellCallback.names = SocialCalc.SpreadsheetControlNamesRangeChange;
   SocialCalc.SpreadsheetControlNamesRangeChange(s.editor);
   SocialCalc.SpreadsheetControlNamesFillNameList();
   SocialCalc.SpreadsheetControlNamesChangedName();
   }

SocialCalc.SpreadsheetControlNamesFillNameList = function() {
   var SCLoc = SocialCalc.LocalizeString;
   var name, i;
   var namelist = [];
   var s=SocialCalc.GetSpreadsheetControlObject();
   var nl = document.getElementById(s.idPrefix+"nameslist");
   var currentname = document.getElementById(s.idPrefix+"namesname").value.toUpperCase().replace(/[^A-Z0-9_\.]/g, "");
   for (name in s.sheet.names) {
      namelist.push(name);
      }
   namelist.sort();
   nl.length = 0;
   if (namelist.length > 0) {
      nl.options[0] = new Option(SCLoc("[New]"));
      }
   else {
      nl.options[0] = new Option(SCLoc("[None]"));
      }
   for (i=0; i<namelist.length; i++) {
      name = namelist[i];
      nl.options[i+1] = new Option(name, name);
      if (name == currentname) {
         nl.options[i+1].selected = true;
         }
      }
   if (currentname == "") {
      nl.options[0].selected = true;
      }
   }

SocialCalc.SpreadsheetControlNamesChangedName = function() {
   var s=SocialCalc.GetSpreadsheetControlObject();
   var nl = document.getElementById(s.idPrefix+"nameslist");
   var name = nl.options[nl.selectedIndex].value;
   if (s.sheet.names[name]) {
      document.getElementById(s.idPrefix+"namesname").value = name;
      document.getElementById(s.idPrefix+"namesdesc").value = s.sheet.names[name].desc || "";
      document.getElementById(s.idPrefix+"namesvalue").value = s.sheet.names[name].definition || "";
      }
   else {
      document.getElementById(s.idPrefix+"namesname").value = "";
      document.getElementById(s.idPrefix+"namesdesc").value = "";
      document.getElementById(s.idPrefix+"namesvalue").value = "";
      }
   }

SocialCalc.SpreadsheetControlNamesRangeChange = function(editor) {
   var s = SocialCalc.GetSpreadsheetControlObject();
   var ele = document.getElementById(s.idPrefix+"namesrangeproposal");
   if (editor.range.hasrange) {
      ele.value = SocialCalc.crToCoord(editor.range.left, editor.range.top) + ":" +
                            SocialCalc.crToCoord(editor.range.right, editor.range.bottom);
      }
   else {
      ele.value = editor.ecell.coord;
      }
   }

SocialCalc.SpreadsheetControlNamesOnunclick = function(s, t) {
   delete s.editor.RangeChangeCallback.names;
   delete s.editor.MoveECellCallback.names;
   }

SocialCalc.SpreadsheetControlNamesSetValue = function() {
   var s = SocialCalc.GetSpreadsheetControlObject();
   document.getElementById(s.idPrefix+"namesvalue").value = document.getElementById(s.idPrefix+"namesrangeproposal").value;
   SocialCalc.KeyboardFocus();
   }

SocialCalc.SpreadsheetControlNamesSave = function() {
   var s = SocialCalc.GetSpreadsheetControlObject();
   var name = document.getElementById(s.idPrefix+"namesname").value;
   SocialCalc.SetTab(s.tabs[0].name); // return to first tab
   SocialCalc.KeyboardFocus();
   if (name != "") {
      s.ExecuteCommand("name define "+name+" "+document.getElementById(s.idPrefix+"namesvalue").value+"\n"+
         "name desc "+name+" "+document.getElementById(s.idPrefix+"namesdesc").value);
      }
   }

SocialCalc.SpreadsheetControlNamesDelete = function() {
   var s = SocialCalc.GetSpreadsheetControlObject();
   var name = document.getElementById(s.idPrefix+"namesname").value;
   SocialCalc.SetTab(s.tabs[0].name); // return to first tab
   SocialCalc.KeyboardFocus();
   if (name != "") {
      s.ExecuteCommand("name delete "+name);
//      document.getElementById(s.idPrefix+"namesname").value = "";
//      document.getElementById(s.idPrefix+"namesvalue").value = "";
//      document.getElementById(s.idPrefix+"namesdesc").value = "";
//      SocialCalc.SpreadsheetControlNamesFillNameList();
      }
   SocialCalc.KeyboardFocus();
   }

// Clipboard

SocialCalc.SpreadsheetControlClipboardOnclick = function(s, t) {
   var s = SocialCalc.GetSpreadsheetControlObject(s.idPrefix);
   clipele = document.getElementById(s.idPrefix+"clipboardtext");
   document.getElementById(s.idPrefix+"clipboardformat-tab").checked = true;
   clipele.value = SocialCalc.ConvertSaveToOtherFormat(SocialCalc.Clipboard.clipboard, "tab");
   return;
   }

SocialCalc.SpreadsheetControlClipboardFormat = function(which) {
   var s = SocialCalc.GetSpreadsheetControlObject();
   clipele = document.getElementById(s.idPrefix+"clipboardtext");
   clipele.value = SocialCalc.ConvertSaveToOtherFormat(SocialCalc.Clipboard.clipboard, which);
   }

SocialCalc.SpreadsheetControlClipboardLoad = function(target) {
   var s = SocialCalc.GetSpreadsheetControlObject(target.getAttribute('data-spreadsheet'));
   var savetype = "tab";
   SocialCalc.SetTab(target, s.tabs[0].name); // return to first tab
   SocialCalc.KeyboardFocus();
   if (document.getElementById(s.idPrefix+"clipboardformat-csv").checked) {
      savetype = "csv";
      }
   else if (document.getElementById(s.idPrefix+"clipboardformat-scsave").checked) {
      savetype = "scsave";
      }
   s.editor.EditorScheduleSheetCommands("loadclipboard "+
      SocialCalc.encodeForSave(
         SocialCalc.ConvertOtherFormatToSave(document.getElementById(s.idPrefix+"clipboardtext").value, savetype)), true, false);
   }

SocialCalc.SpreadsheetControlClipboardClear = function() {
   var s = SocialCalc.GetSpreadsheetControlObject();
   var clipele = document.getElementById(s.idPrefix+"clipboardtext");
   clipele.value = "";
   s.editor.EditorScheduleSheetCommands("clearclipboard", true, false);
   clipele.focus();
   }

SocialCalc.SpreadsheetControlClipboardExport = function() {
   var s = SocialCalc.GetSpreadsheetControlObject();
   if (s.ExportCallback) {
      s.ExportCallback(s);
      }
   SocialCalc.SetTab(s.tabs[0].name); // return to first tab
   SocialCalc.KeyboardFocus();
   }

// Settings

SocialCalc.SpreadsheetControlSettingsSwitch = function(target) {
   SocialCalc.SettingControlReset();
   var s = SocialCalc.GetSpreadsheetControlObject();
   var sheettable = document.getElementById(s.idPrefix+"sheetsettingstable");
   var celltable = document.getElementById(s.idPrefix+"cellsettingstable");
   var sheettoolbar = document.getElementById(s.idPrefix+"sheetsettingstoolbar");
   var celltoolbar = document.getElementById(s.idPrefix+"cellsettingstoolbar");
   if (target=="sheet") {
      sheettable.style.display = "block";
      celltable.style.display = "none";
      sheettoolbar.style.display = "block";
      celltoolbar.style.display = "none";
      SocialCalc.SettingsControlSetCurrentPanel(s.views.settings.values.sheetspanel);
      }
   else {
      sheettable.style.display = "none";
      celltable.style.display = "block";
      sheettoolbar.style.display = "none";
      celltoolbar.style.display = "block";
      SocialCalc.SettingsControlSetCurrentPanel(s.views.settings.values.cellspanel);
      }
   }

SocialCalc.SettingsControlSave = function(target) {
   var range, cmdstr;
   var s = SocialCalc.GetSpreadsheetControlObject();
   var sc = SocialCalc.SettingsControls;
   var panelobj = sc.CurrentPanel;
   var attribs = SocialCalc.SettingsControlUnloadPanel(panelobj);

   SocialCalc.SetTab(target, s.tabs[0].name); // return to first tab
   SocialCalc.KeyboardFocus();

   if (target=="sheet") {
      cmdstr = s.sheet.DecodeSheetAttributes(attribs);
      }
   else if (target=="cell") {
      if (s.editor.range.hasrange) {
         range = SocialCalc.crToCoord(s.editor.range.left, s.editor.range.top) + ":" +
            SocialCalc.crToCoord(s.editor.range.right, s.editor.range.bottom);
         }
      cmdstr = s.sheet.DecodeCellAttributes(s.editor.ecell.coord, attribs, range);
      }
   else { // Cancel
      }
   if (cmdstr) {
      s.editor.EditorScheduleSheetCommands(cmdstr, true, false);
      }
   }

///////////////////////
//
// SAVE / LOAD ROUTINES
//
///////////////////////

//
// result = SocialCalc.SpreadsheetControlCreateSpreadsheetSave(spreadsheet, otherparts)
//
// Saves the spreadsheet's sheet data, editor settings, and audit trail (redo stack).
// The serialized data strings are concatenated together in multi-part MIME format.
// The first part lists the types of the subsequent parts (e.g., "sheet", "editor", and "audit")
// in this format:
//   # comments
//   version:1.0
//   part:type1
//   part:type2
//   ...
//
// If otherparts is non-null, it is an object with:
//   partname1: "part contents - should end with \n",
//   partname2: "part contents - should end with \n"
//


SocialCalc.SpreadsheetControlCreateSpreadsheetSave = function(spreadsheet, otherparts) {

   var result;

   var otherpartsstr = "";
   var otherpartsnames = "";
   var partname, extranl;

   if (otherparts) {
      for (partname in otherparts) {
         if (otherparts[partname].charAt(otherparts[partname]-1) != "\n") {
            extranl = "\n";
            }
         else {
            extranl = "";
            }
         otherpartsstr += "--" + spreadsheet.multipartBoundary + "\nContent-type: text/plain; charset=UTF-8\n\n" +
            otherparts[partname] + extranl;
         otherpartsnames += "part:"+partname + "\n";
         }
      }

   result = "socialcalc:version:1.0\n" +
      "MIME-Version: 1.0\nContent-Type: multipart/mixed; boundary="+
      spreadsheet.multipartBoundary + "\n" +
      "--" + spreadsheet.multipartBoundary + "\nContent-type: text/plain; charset=UTF-8\n\n" +
      "# SocialCalc Spreadsheet Control Save\nversion:1.0\npart:sheet\npart:edit\npart:audit\n" + otherpartsnames +
      "--" + spreadsheet.multipartBoundary + "\nContent-type: text/plain; charset=UTF-8\n\n" +
      spreadsheet.CreateSheetSave() +
      "--" + spreadsheet.multipartBoundary + "\nContent-type: text/plain; charset=UTF-8\n\n" +
      spreadsheet.editor.SaveEditorSettings() +
      "--" + spreadsheet.multipartBoundary + "\nContent-type: text/plain; charset=UTF-8\n\n" +
      spreadsheet.sheet.CreateAuditString() +
      otherpartsstr +
      "--" + spreadsheet.multipartBoundary + "--\n";

   return result;

   }


//
// parts = SocialCalc.SpreadsheetControlDecodeSpreadsheetSave(spreadsheet, str)
//
// Separates the parts from a spreadsheet save string, returning an object with the sub-strings.
//
//    {type1: {start: startpos, end: endpos}, type2:...}
//

SocialCalc.SpreadsheetControlDecodeSpreadsheetSave = function(spreadsheet, str) {

   var pos1, mpregex, searchinfo, boundary, boundaryregex, blanklineregex, start, ending, lines, i, lines, p, pnun;
   var parts = {};
   var partlist = [];

   pos1 = str.search(/^MIME-Version:\s1\.0/mi);
   if (pos1 < 0) return parts;

   mpregex = /^Content-Type:\s*multipart\/mixed;\s*boundary=(\S+)/mig;
   mpregex.lastIndex = pos1;

   searchinfo = mpregex.exec(str);
   if (mpregex.lastIndex <= 0) return parts;
   boundary = searchinfo[1];

   boundaryregex = new RegExp("^--"+boundary+"(?:\r\n|\n)", "mg");
   boundaryregex.lastIndex = mpregex.lastIndex;

   searchinfo = boundaryregex.exec(str); // find header top boundary
   blanklineregex = /(?:\r\n|\n)(?:\r\n|\n)/gm;
   blanklineregex.lastIndex = boundaryregex.lastIndex;
   searchinfo = blanklineregex.exec(str); // skip to after blank line
   if (!searchinfo) return parts;
   start = blanklineregex.lastIndex;
   boundaryregex.lastIndex = start;
   searchinfo = boundaryregex.exec(str); // find end of header
   if (!searchinfo) return parts;
   ending = searchinfo.index;

   lines = str.substring(start, ending).split(/\r\n|\n/); // get header as lines
   for (i=0;i<lines.length;i++) {
      line=lines[i];
      p = line.split(":");
      switch (p[0]) {
         case "version":
            break;
         case "part":
            partlist.push(p[1]);
            break;
         }
      }

   for (pnum=0; pnum<partlist.length; pnum++) { // get each part
      blanklineregex.lastIndex = ending;
      searchinfo = blanklineregex.exec(str); // find blank line ending mime-part header
      if (!searchinfo) return parts;
      start = blanklineregex.lastIndex;
      if (pnum==partlist.length-1) { // last one has different boundary
         boundaryregex = new RegExp("^--"+boundary+"--$", "mg");
         }
      boundaryregex.lastIndex = start;
      searchinfo = boundaryregex.exec(str); // find ending boundary
      if (!searchinfo) return parts;
      ending = searchinfo.index;
      parts[partlist[pnum]] = {start: start, end: ending}; // return position within full string
      }

   return parts;

   }


/*
* SettingsControls
*
* Each settings panel has an object in the following form:
*
*    {ctrl-name1: {setting: setting-nameA, type: ctrl-type, id: id-component},
*     ctrl-name2: {setting: setting-nameB, type: ctrl-type, id: id-component, initialdata: optional-initialdata-override},
*     ...}
*
* The ctrl-types are names that correspond to:
*
*    SocialCalc.SettingsControls.Controls = {
*       ctrl-type1: {
*          SetValue: function(panel-obj, ctrl-name, {def: true/false, val: value}) {...;},
*          ColorValues: if true, Onchanged converts between hex and RGB
*          GetValue: function(panel-obj, ctrl-name) {...return {def: true/false, val: value};},
*          Initialize: function(panel-obj, ctrl-name) {...;}, // used to fill dropdowns, etc.
*          InitialData: control-dependent, // used by Initialize (if no panel ctrlname.initialdata)
*          OnReset: function(ctrl-name) {...;}, // called to put down popups, etc.
*          ChangedCallback: function(ctrl-name) {...;} // if not null, called by control when user changes value
*       }
*
*/

SocialCalc.SettingsControls = {
   Controls: {},
   CurrentPanel: null // panel object to search on events
   };

//
// SocialCalc.SettingsControlSetCurrentPanel(panel-object)
//

SocialCalc.SettingsControlSetCurrentPanel = function(panelobj) {

   SocialCalc.SettingsControls.CurrentPanel = panelobj;

   SocialCalc.SettingsControls.PopupChangeCallback({panelobj: panelobj}, "", null);

   }


//
// SocialCalc.SettingsControlInitializePanel(panel-object)
//

SocialCalc.SettingsControlInitializePanel = function(panelobj) {

   var ctrlname;
   var sc = SocialCalc.SettingsControls;

   for (ctrlname in panelobj) {
      if (ctrlname=="name") continue;
      ctrl = sc.Controls[panelobj[ctrlname].type];
      if (ctrl && ctrl.Initialize) ctrl.Initialize(panelobj, ctrlname);
      }

   }


//
// SocialCalc.SettingsControlLoadPanel(panel-object, attribs)
//

SocialCalc.SettingsControlLoadPanel = function(panelobj, attribs) {

   var ctrlname;
   var sc = SocialCalc.SettingsControls;

   for (ctrlname in panelobj) {
      if (ctrlname=="name") continue;
      ctrl = sc.Controls[panelobj[ctrlname].type];
      if (ctrl && ctrl.SetValue) ctrl.SetValue(panelobj, ctrlname, attribs[panelobj[ctrlname].setting]);
      }

   }

//
// attribs = SocialCalc.SettingsControlUnloadPanel(panel-object)
//

SocialCalc.SettingsControlUnloadPanel = function(panelobj) {

   var ctrlname;
   var sc = SocialCalc.SettingsControls;
   var attribs = {};

   for (ctrlname in panelobj) {
      if (ctrlname=="name") continue;
      ctrl = sc.Controls[panelobj[ctrlname].type];
      if (ctrl && ctrl.GetValue) attribs[panelobj[ctrlname].setting] = ctrl.GetValue(panelobj, ctrlname);
      }

   return attribs;

   }

//
// SocialCalc.SettingsControls.PopupChangeCallback
//

SocialCalc.SettingsControls.PopupChangeCallback = function(attribs, id, value) {

   var sc = SocialCalc.Constants;

   var ele = document.getElementById("sample-text");

   if (!ele || !attribs || !attribs.panelobj) return;

   var idPrefix = SocialCalc.CurrentSpreadsheetControlObject.idPrefix;

   var c = attribs.panelobj.name == "cell" ? "c" : "";

   var v, a, parts, str1, str2, i;

   parts = sc.defaultCellLayout.match(/^padding.(\S+) (\S+) (\S+) (\S+).vertical.align.(\S+);$/) || [];

   var cv = {color: ["textcolor"], backgroundColor: ["bgcolor", "#FFF"],
             fontSize: ["fontsize", sc.defaultCellFontSize], fontFamily: ["fontfamily"],
             paddingTop: ["padtop", parts[1]], paddingRight: ["padright", parts[2]],
             paddingBottom: ["padbottom", parts[3]], paddingLeft: ["padleft", parts[4]],
             verticalAlign: ["alignvert", parts[5]]};

   for (a in cv) {
      v = SocialCalc.Popup.GetValue(idPrefix+c+cv[a][0]) || cv[a][1] || "";
      ele.style[a] = v;
      }

   if (c=="c") {
      cv = {borderTop: "cbt", borderRight: "cbr", borderBottom: "cbb", borderLeft: "cbl"};
      for (a in cv) {
         v = SocialCalc.SettingsControls.BorderSideGetValue(attribs.panelobj, cv[a]);
         ele.style[a] = v ? (v.val || "") : "";
         }
      v = SocialCalc.Popup.GetValue(idPrefix+"calignhoriz");
      ele.style.textAlign = v || "left";
      ele.childNodes[1].style.textAlign = v || "right";
      }
   else {
      ele.style.border = "";
      v = SocialCalc.Popup.GetValue(idPrefix+"textalignhoriz");
      ele.style.textAlign = v || "left";
      v = SocialCalc.Popup.GetValue(idPrefix+"numberalignhoriz");
      ele.childNodes[1].style.textAlign = v || "right";
      }

   v = SocialCalc.Popup.GetValue(idPrefix+c+"fontlook");
   parts = v ? (v.match(/^(\S+) (\S+)$/) || []) : [];
   ele.style.fontStyle = parts[1] || "";
   ele.style.fontWeight = parts[2] || "";

   v = SocialCalc.Popup.GetValue(idPrefix+c+"formatnumber") || "General";
   str1 = SocialCalc.FormatNumber.formatNumberWithFormat(9.8765, v, "");
   str2 = SocialCalc.FormatNumber.formatNumberWithFormat(-1234.5, v, "");
   if (str2 != "??-???-??&nbsp;??:??:??") { // not bad date from negative number
      str1 += "<br>"+str2;
      }
      
   ele.childNodes[1].innerHTML = str1;

   }

//
// PopupList Control
//

SocialCalc.SettingsControls.PopupListSetValue = function(panelobj, ctrlname, value) {

   if (!value) {alert(ctrlname+" no value"); return;}

   var sp = SocialCalc.Popup;

   if (!value.def) {
      sp.SetValue(panelobj[ctrlname].id, value.val);
      }
   else {
      sp.SetValue(panelobj[ctrlname].id, "");
      }

   }

//
// SocialCalc.SettingsControls.PopupListGetValue
//

SocialCalc.SettingsControls.PopupListGetValue = function(panelobj, ctrlname) {

   var ctl = panelobj[ctrlname];
   if (!ctl) return null;

   var value = SocialCalc.Popup.GetValue(ctl.id);
   if (value) {
      return {def: false, val: value};
      }
   else {
      return {def: true, val: 0};
      }

   }

//
// SocialCalc.SettingsControls.PopupListInitialize
//

SocialCalc.SettingsControls.PopupListInitialize = function(panelobj, ctrlname) {

   var i, val, pos, otext;
   var sc = SocialCalc.SettingsControls;
   var initialdata = panelobj[ctrlname].initialdata || sc.Controls[panelobj[ctrlname].type].InitialData || "";
   initialdata = SocialCalc.LocalizeSubstrings(initialdata);
   var optionvals = initialdata.split(/\|/);

   var options = [];

   for (i=0; i<(optionvals.length||0); i++) {
      val = optionvals[i];
      pos = val.indexOf(":");
      otext = val.substring(0, pos);
      if (otext.indexOf("\\")!=-1) { // escape any colons
         otext = otext.replace(/\\c/g,":");
         otext = otext.replace(/\\b/g,"\\");

         }
      otext = SocialCalc.special_chars(otext);
      if (otext == "[custom]") {
         options[i] = {o: SocialCalc.Constants.s_PopupListCustom, v: val.substring(pos+1), a:{custom: true}};
         }
      else if (otext == "[cancel]") {
         options[i] = {o: SocialCalc.Constants.s_PopupListCancel, v: "", a:{cancel: true}};
         }
      else if (otext == "[break]") {
         options[i] = {o: "-----", v: "", a:{skip: true}};
         }
      else if (otext == "[newcol]") {
         options[i] = {o: "", v: "", a:{newcol: true}};
         }
      else {
         options[i] = {o: otext, v: val.substring(pos+1)};
         }
      }

   SocialCalc.Popup.Create("List", panelobj[ctrlname].id, {});
   SocialCalc.Popup.Initialize(panelobj[ctrlname].id, 
      {options: options, 
       attribs:{changedcallback: SocialCalc.SettingsControls.PopupChangeCallback, panelobj: panelobj}});

   }


//
// SocialCalc.SettingsControls.PopupListReset
//

SocialCalc.SettingsControls.PopupListReset = function(ctrlname) {

   SocialCalc.Popup.Reset("List");

   }

SocialCalc.SettingsControls.Controls.PopupList = {
   SetValue: SocialCalc.SettingsControls.PopupListSetValue,
   GetValue: SocialCalc.SettingsControls.PopupListGetValue,
   Initialize: SocialCalc.SettingsControls.PopupListInitialize,
   OnReset: SocialCalc.SettingsControls.PopupListReset,
   ChangedCallback: null
   }

//
// ColorChooser Control
//

SocialCalc.SettingsControls.ColorChooserSetValue = function(panelobj, ctrlname, value) {

   if (!value) {alert(ctrlname+" no value"); return;}

   var sp = SocialCalc.Popup;

   if (!value.def) {
      sp.SetValue(panelobj[ctrlname].id, value.val);
      }
   else {
      sp.SetValue(panelobj[ctrlname].id, "");
      }

   }

//
// SocialCalc.SettingsControls.ColorChooserGetValue
//

SocialCalc.SettingsControls.ColorChooserGetValue = function(panelobj, ctrlname) {

   var value = SocialCalc.Popup.GetValue(panelobj[ctrlname].id);
   if (value) {
      return {def: false, val: value};
      }
   else {
      return {def: true, val: 0};
      }

   }

//
// SocialCalc.SettingsControls.ColorChooserInitialize
//

SocialCalc.SettingsControls.ColorChooserInitialize = function(panelobj, ctrlname) {

   var i, val, pos, otext;
   var sc = SocialCalc.SettingsControls;

   SocialCalc.Popup.Create("ColorChooser", panelobj[ctrlname].id, {});
   SocialCalc.Popup.Initialize(panelobj[ctrlname].id,
      {attribs:{title: "&nbsp;", moveable: true, width: "106px",
                changedcallback: SocialCalc.SettingsControls.PopupChangeCallback, panelobj: panelobj}});

   }


//
// SocialCalc.SettingsControls.ColorChooserReset
//

SocialCalc.SettingsControls.ColorChooserReset = function(ctrlname) {

   SocialCalc.Popup.Reset("ColorChooser");

   }

SocialCalc.SettingsControls.Controls.ColorChooser = {
   SetValue: SocialCalc.SettingsControls.ColorChooserSetValue,
   GetValue: SocialCalc.SettingsControls.ColorChooserGetValue,
   Initialize: SocialCalc.SettingsControls.ColorChooserInitialize,
   OnReset: SocialCalc.SettingsControls.ColorChooserReset,
   ChangedCallback: null
   }


//
// SocialCalc.SettingsControls.BorderSideSetValue
//

SocialCalc.SettingsControls.BorderSideSetValue = function(panelobj, ctrlname, value) {

   var sc = SocialCalc.SettingsControls;
   var ele, found, idname, parts;
   var idstart = panelobj[ctrlname].id;

   if (!value) {alert(ctrlname+" no value"); return;}

   ele = document.getElementById(idstart+"-onoff-bcb"); // border checkbox
   if (!ele) return;

   if (value.val) { // border does not use default: it looks only to the value currently
      ele.checked = true;
      ele.value = value.val;
      parts = value.val.match(/(\S+)\s+(\S+)\s+(\S.+)/);
      idname = idstart+"-color";
      SocialCalc.Popup.SetValue(idname, parts[3]);
      SocialCalc.Popup.SetDisabled(idname, false);
      }
   else {
      ele.checked = false;
      ele.value = value.val;
      idname = idstart+"-color";
      SocialCalc.Popup.SetValue(idname, "");
      SocialCalc.Popup.SetDisabled(idname, true);
      }

   }

//
// SocialCalc.SettingsControls.BorderSideGetValue
//

SocialCalc.SettingsControls.BorderSideGetValue = function(panelobj, ctrlname) {

   var sc = SocialCalc.SettingsControls;
   var ele, value;
   var idstart = panelobj[ctrlname].id;

   ele = document.getElementById(idstart+"-onoff-bcb"); // border checkbox
   if (!ele) return;


   if (ele.checked) { // on
      value = SocialCalc.Popup.GetValue(idstart+"-color");
      value = "1px solid " + (value || "rgb(0,0,0)");
      return {def: false, val: value};
      }
   else { // off
      return {def: false, val: ""};
      }

   }

//
// SocialCalc.SettingsControls.BorderSideInitialize
//

SocialCalc.SettingsControls.BorderSideInitialize = function(panelobj, ctrlname) {

   var sc = SocialCalc.SettingsControls;
   var idstart = panelobj[ctrlname].id;

   SocialCalc.Popup.Create("ColorChooser", idstart+"-color", {});
   SocialCalc.Popup.Initialize(idstart+"-color",
      {attribs:{title: "&nbsp;", width: "106px", moveable: true,
                changedcallback: SocialCalc.SettingsControls.PopupChangeCallback, panelobj: panelobj}});

   }


//
// SocialCalc.SettingsControlOnchangeBorder = function(ele)
//

SocialCalc.SettingsControlOnchangeBorder = function(ele) {

   var idname, value, found, ele2;
   var sc = SocialCalc.SettingsControls;
   var panelobj = sc.CurrentPanel;

   var nameparts = ele.id.match(/(^.*\-)(\w+)\-(\w+)\-(\w+)$/);
   if (!nameparts) return;
   var prefix = nameparts[1];
   var ctrlname = nameparts[2];
   var ctrlsubid = nameparts[3]
   var ctrlidsuffix = nameparts[4];
   var ctrltype = panelobj[ctrlname].type;

   switch (ctrlidsuffix) {
      case "bcb": // border checkbox
         if (ele.checked) {
            sc.Controls[ctrltype].SetValue(sc.CurrentPanel, ctrlname, {def: false, val: ele.value || "1px solid rgb(0,0,0)"});
            }
         else {
            sc.Controls[ctrltype].SetValue(sc.CurrentPanel, ctrlname, {def: false, val: ""});
            }
         break;
      }

   }


SocialCalc.SettingsControls.Controls.BorderSide = {
   SetValue: SocialCalc.SettingsControls.BorderSideSetValue,
   GetValue: SocialCalc.SettingsControls.BorderSideGetValue,
   OnClick: SocialCalc.SettingsControls.ColorComboOnClick,
   Initialize: SocialCalc.SettingsControls.BorderSideInitialize,
   InitialData: {thickness: "1 pixel:1px", style: "Solid:solid"},
   ChangedCallback: null
   }


SocialCalc.SettingControlReset = function() {

   var sc = SocialCalc.SettingsControls;
   var ctrlname;

   for (ctrlname in sc.Controls) {
      if (sc.Controls[ctrlname].OnReset) sc.Controls[ctrlname].OnReset(ctrlname);
      }
   }


/**********************
*
* CtrlSEditor implementation for editing SocialCalc.OtherSaveParts
*
*/

SocialCalc.OtherSaveParts = {}; // holds other parts to save - must be set when loaded if you want to keep

SocialCalc.CtrlSEditor = function(whichpart) {

   var strtoedit, partname;
   if (whichpart.length > 0) {
      strtoedit = SocialCalc.special_chars(SocialCalc.OtherSaveParts[whichpart] || "");
      }
   else {
      strtoedit = "Listing of Parts\n";
      for (partname in SocialCalc.OtherSaveParts) {
         strtoedit += SocialCalc.special_chars("\nPart: "+partname+"\n=====\n"+SocialCalc.OtherSaveParts[partname]+"\n");
         }
      }
   var editbox = document.createElement("div");
   editbox.style.cssText = "position:absolute;z-index:500;width:300px;height:300px;left:100px;top:200px;border:1px solid black;background-color:#EEE;text-align:center;";
   editbox.id = "socialcalc-editbox";
   editbox.innerHTML = whichpart+'<br><br><textarea id="socialcalc-editbox-textarea" style="width:250px;height:200px;">'+
      strtoedit + '</textarea><br><br><input type=button ' +
      'onclick="SocialCalc.CtrlSEditorDone (\'socialcalc-editbox\', \''+whichpart+'\');" value="OK">';
   document.body.appendChild(editbox);

   var ebta = document.getElementById("socialcalc-editbox-textarea");
   ebta.focus();
   SocialCalc.CmdGotFocus(ebta);

   }

SocialCalc.CtrlSEditorDone = function(idprefix, whichpart) {

   var edittextarea = document.getElementById(idprefix+"-textarea");
   var text = edittextarea.value;
   if (whichpart.length > 0) {
      if (text.length > 0) {
         SocialCalc.OtherSaveParts[whichpart] = text;
         }
      else {
         delete SocialCalc.OtherSaveParts[whichpart];
         }
      }

   var editbox = document.getElementById(idprefix);
   SocialCalc.KeyboardFocus();
   editbox.parentNode.removeChild(editbox);

   }

