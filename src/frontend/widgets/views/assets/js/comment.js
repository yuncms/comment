/**
 * 发布评论
 * @param model_class
 * @param model_id
 * @param content
 * @param to_user_id
 */
function add_comment(model_class, model_id, content, to_user_id) {
    var postData = {model_id: model_id, model_class: model_class, content: content};
    if (to_user_id > 0) {
        postData.to_user_id = to_user_id;
    }
    jQuery.post('/comment/default/store', postData, function (html) {
        jQuery("#comments-" + model_class + "-" + model_id + " .widget-comment-list").append(html);
        jQuery("#comment-" + model_class + "-content-" + model_id).val('');
    });
}

/**
 * 加载评论
 * @param model_class
 * @param model_id
 */
function load_comments(model_class, model_id) {
    jQuery.get('/comment/default/list', {
        model_id: model_id, model_class: model_class
    }, function (html) {
        jQuery("#comments-" + model_class + "-" + model_id + " .widget-comment-list").append(html);
    });
}

/**
 * 清除评论
 * @param model_class
 * @param model_id
 */
function clear_comments(model_class, model_id) {
    jQuery("#comments-" + model_class + "-" + model_id + " .widget-comment-list").empty();
}

/*评论提交*/
jQuery(".comment-btn").click(function () {
    var model_id = jQuery(this).data('model_id');
    var model_class = jQuery(this).data('model_class');
    var to_user_id = jQuery(this).data('to_user_id');
    var content = jQuery("#comment-" + model_class + "-content-" + model_id).val();
    add_comment(model_class, model_id, content, to_user_id);
    jQuery("#comment-content-" + model_id + "").val('');
});
