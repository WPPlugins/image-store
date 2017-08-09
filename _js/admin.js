/**
 * Image Store - Admin
 *
 * @file admin.js
 * @package Image Store
 * @author Hafid Trujillo
 * @copyright 2010-2016
 * @filesource  wp-content/plugins/image-store/_js/admin.js
 * @since 3.0.0
 */

jQuery(document).ready(function(i){i(".ims-box").hide(),i(".ims-box").eq(0).show(),i(".ims-tabs li").eq(0).addClass("current"),(hash=window.location.hash)&&(i(".ims-box:visible").hide(),i(".ims-tabs li.current").removeClass("current"),index=i(".ims-tabs li a").index(i('a[href|="'+hash+'"]')),i(".ims-tabs li").eq(index).addClass("current"),i(hash).show()),i(".ims-tabs li").click(function(s){s.preventDefault(),i("#message").remove(),i(".ims-box:visible").hide(),i(".ims-tabs li.current ").removeClass("current"),i(".ims-box").eq(i(".ims-tabs li").index(i(this))).fadeIn(),i(this).addClass("current");var e={};e.title=document.title,e.url=i(this).find("a:eq(0)").attr("href"),history.pushState(e,e.title,e.url)}),i("select[name=userid]").change(function(){i(this).val()>0&&(window.location.hash="permissions",window.location.search="post_type=ims_gallery&page=ims-settings&userid="+i(this).val(),window.location.href=window.location)})});
