/**
 * Image Store - imstore tinymce outter functions
 *
 * @file tinymce.js
 * @package Image Store
 * @author Hafid Trujillo
 * @copyright 2010-2016
 * @filesource  wp-content/plugins/image-store/_js/tinymce/tinymce.js
 * @since 3.0.0
 */
 
var win=window.dialogArguments||opener||parent||top;!function(e){if("undefined"!=typeof win.tinyMCE){var n=win.tinyMCE.activeEditor;ims_close_window=function(){n.windowManager.close(window)},e("#image-store-embed").submit(function(i){i.preventDefault(),n.focus(),opts="",e.each(e(this).serializeArray(),function(e,n){0!=n.value&&(opts+=" "+n.name+"="+n.value)}),n.insertContent("[ims-gallery"+opts+"]"),n.nodeChanged(),ims_close_window()}),e("#search-results").delegate("li","click",function(){e("#galid").val(e(this).find("span.id").html())}),e("#internal-toggle").click(function(){e("#search-panel:hidden").length>0?(e("#search-panel").show("slow"),win.resize_gal_window("s")):(e("#search-panel").hide(),win.resize_gal_window())}),search_gals=function(){e.get(imslocal.imsajax,{action:"searchgals",_wpnonce:imslocal.nonceajax,q:e("#search-field").val()},function(n){e("#search-results ul li").remove(),e("#search-results ul").append(n),e(".link-search-wrapper .waiting").hide()})},search_gals(),e("#search-field").keyup(function(){s=e(this).val(),s.length>2&&(e(".link-search-wrapper .waiting").show(),search_gals())})}}(jQuery);