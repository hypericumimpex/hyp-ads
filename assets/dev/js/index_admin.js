import '../css/_ning_admin.css';
import '../css/_ning_uploader.css';
import '../../packages/coloring_pick/jquery.coloring-pick.min.js.css';
import '../../../include/widgets/chosen/chosen.css';
import '../../../include/widgets/checkbox/checkbox.css';


import Adning_global from './_ning_admin.js';
import './_ning_uploader.js';
import '../../packages/coloring_pick/jquery.coloring-pick.min.js';
import '../../../include/widgets/tooltipster/tooltipster.bundle.min.js';
import '../../../include/widgets/chosen/chosen.jquery.min.js';
import '../../../include/widgets/chosen/jquery-chosen-sortable.min.js';

import codemirror from 'codemirror';
import 'codemirror/mode/javascript/javascript';
import 'codemirror/mode/css/css';
import 'codemirror/mode/htmlmixed/htmlmixed';
import 'codemirror/lib/codemirror.css';
import '../css/cm_github.css';


window.Adning_global = Adning_global.Adning_global;


jQuery(document).ready(function($) {
    
    /*
     * ADD Codemirror to all code textareas
    */
   	var code_editors = document.querySelectorAll(".code_editor");

   	for (var i = 0; i < code_editors.length; i++) {
	  
	   	var editor = codemirror.fromTextArea(code_editors[i], {
			lineNumbers : true,
			mode: code_editors[i].getAttribute("data-lang"),
			theme: "github"
	   	});
	   	editor.setSize("100%","200px");
   	}
});