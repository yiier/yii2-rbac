$(function () {

    // 添加权限
    function createPermission(permission, des, check, rule_name) {
        var url = $('.permission-url').attr('href');
        $.get(url, {permission: permission, des: des, check: check, rule_name: rule_name}, function (e) {
        }, 'json');
    }

    // 加入 权限列表
    $('.permission-action').click(function () {
        var action = $(this).val();
        var check = $(this).is(":checked");
        var des = $(this).parent('span').prev('.permission-action-des').val();
        var rule_name = $(this).parent('span').prev('.permission-action-rule_name').val();
        createPermission(action, des, check, rule_name);
    });

    // 添加 权限描述、rule path
    $('.permission-action-rule_name, .permission-action-des').blur(function () {
        var permission = $(this).parent().find('.permission-name');
        var action = permission.val();
        var des = $(this).val();
        var rule_name = $(this).parent().find(".permission-action-rule_name").val();
        permission.attr('checked', 'checked');
        createPermission(action, des, true, rule_name);
    });

    // 添加权限
    function createRule(check, class_name, name) {
        var url = $('.rule-url').attr('href');
        $.get(url, {name: name, check: check, class_name: class_name}, function (e) {
        }, 'json');
    }

    // 加入 权限列表
    $('.rule-class_name').click(function () {
        var class_name = $(this).val();
        var check = $(this).is(":checked");
        var name = $(this).siblings('.rule-action-name').val();
        createRule(check, class_name, name);
    });

    // 添加 权限描述、rule path
    $('.rule-action-name').blur(function () {
        var name = $(this).val();
        var class_name_node = $(this).siblings('.rule-class_name');
        class_name_node.attr('checked', 'checked');
        createRule(true, class_name_node.val(), name);
    });


    function roleAssignPermission(id, is_sel) {
        var role = $('input[name=role_name]').val();
        var url = $('.role-assign').attr('href');
        var csrf = $('input[name=csrf]').val();
        $.post(url, {role: role, id: id, _csrf: csrf, is_sel: is_sel}, function (xhr) {
            $('input[name=csrf]').val(xhr.csrf);
            if (xhr.status) {
                if (Array.isArray(id)) {
                    for (u in id) {
                        $('li[data-id="' + id[u] + '"]').addClass('selected');
                    }
                } else {
                    $('li[data-id="' + id + '"]').toggleClass('selected');
                }
            }
        }, 'json');
    }

    // 角色分配权限
    $('.assign-permissions').click(function () {
        var id = $(this).data('id');
        var is_sel = $(this).hasClass('selected');
        roleAssignPermission(id, is_sel);
    });
    // 角色全选分配权限
    $('.assign-all-permissions').click(function (e) {
        e.preventDefault();
        var ids = [];
        $(this).parent().next('.u-list').find('.assign-permissions:not(.selected)').each(function (e) {
            ids.push($(this).data('id'));
        });
        if (ids.length) {
            roleAssignPermission(ids, true);
        }
    });

    function roleAssign(user_id, is_sel) {
        var role = $('input[name=role_name]').val();
        var url = $('.role-assign').attr('href');
        var csrf = $('input[name=csrf]').val();
        $.post(url, {role: role, user_id: user_id, _csrf: csrf, is_sel: is_sel}, function (xhr) {
            $('input[name=csrf]').val(xhr.csrf);
            if (xhr.status) {
                if (isNaN(user_id)) {
                    for (u in user_id) {
                        $('li[data-user_id=' + user_id[u] + ']').addClass('selected');
                    }
                } else {
                    $('li[data-user_id=' + user_id + ']').toggleClass('selected');
                }
            }
        }, 'json');
    }

    // 角色分配用户
    $('.assign-user').click(function () {
        var user_id = $(this).data('user_id');
        var is_sel = $(this).hasClass('selected');
        roleAssign(user_id, is_sel);
    });

    // 角色全选分配用户
    $('.assign-all-user').click(function (e) {
        e.preventDefault();
        var user_ids = [];
        $(this).parent().next('.u-list').find(".assign-user:not(.selected)").each(function (e) {
            user_ids.push($(this).data('user_id'));
        });
        if (user_ids.length) {
            roleAssign(user_ids, true);
        }
    });


    $("#tree-view .collapsed").on("click", function () {
        var that = $(this);
        if (that.siblings("ul").hasClass("hidden")) {
            that.siblings("ul").removeClass("hidden");
            that.siblings("ul").addClass("show");
            that.removeClass("fa-plus").addClass("fa-minus");
        } else {
            that.siblings("ul").removeClass("show");
            that.siblings("ul").addClass("hidden");
            that.removeClass("fa-minus").addClass("fa-plus");
        }
    });
});