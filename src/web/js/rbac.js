$(function () {

    // 加入 权限列表
    $('.action').click(function (e) {
        var action = $(this).val();
        var check = $(this).is(":checked");
        var url = $('.permission').attr('href');
        var des = $(this).parent('span').prev('.action-des').val();
        createPermission(action, des, check);
    });
    // 权限描述
    $('.action-des').blur(function () {
        var permission = $(this).parent().find('.permission-name');
        var action = permission.val();
        var des = $(this).val();
        permission.attr('checked', 'checked');
        createPermission(action, des, true);
    });
    // 添加权限
    function createPermission(action, des, check) {
        var url = $('.permission').attr('href');
        $.get(url, {permission: action, des: des, check: check}, function (e) {
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

    $("#collapsed-setting").on("click", function () {
        var that = $(".collapsed");
        var btn = $(".btn-box-tool i");
        var setting = $("#collapsed-setting i");
        if (that.hasClass("collapsed-box")) {
            that.removeClass("collapsed-box");
            btn.removeClass("fa-plus").addClass("fa-minus");
            setting.removeClass("fa-plus").addClass("fa-minus");
        } else {
            that.addClass("collapsed-box");
            btn.removeClass("fa-minus").addClass("fa-plus");
            setting.removeClass("fa-minus").addClass("fa-plus");
        }
    });

});