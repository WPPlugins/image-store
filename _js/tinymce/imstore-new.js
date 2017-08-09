/**
 * Image Store - imstore tinymce plugin
 *
 * @file imstore.js
 * @package Image Store
 * @author Hafid Trujillo
 * @copyright 2010-2016
 * @filesource  wp-content/plugins/image-store/_js/tinymce/imstore-new.js
 * @since 3.5.5
 */
 
var resize_gal_window;!function(e,n,i){var t=_.extend({},{},{edit:function(){n.activeEditor.execCommand("imStoreOpen",this.shortcode.attrs.named),console.log(this.shortcode.attrs.named)},initialize:function(){var e=this;i.get(imsajax.url+"/embed",this.shortcode.attrs.named,function(n){e.render(n)})}});e.register("ims-gallery",_.extend({},t)),tinymce.PluginManager.add("imstore",function(e,n){open_imstore=function(){e.windowManager.open({inline:1,width:510,height:345,file:n+"/tinymce.php"},{plugin_url:n})},e.addCommand("imStoreOpen",function(t){e.windowManager.open({inline:1,width:510,height:345,file:n+"/tinymce.php?"+i.param(t)},{plugin_url:n})}),e.addButton("imstore",{icon:"imstore",context:"insert",onclick:open_imstore,prependToContext:!0})}),resize_gal_window=function(e){$object=i("body > .mce-window"),change="s"==e?170:-170,$body=$object.find(".mce-container-body"),wt=(i(window).height()-($object.height()+change))/2.5,$object.animate({top:wt}),$body.animate({height:$body.height()+change})}}(window.wp.mce.views,window.tinyMCE,window.jQuery);