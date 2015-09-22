$(function () {
    try {
        ace.settings.check('breadcrumbs', 'fixed')
    } catch (e) {
    }

    $('.add-role').click(function (e) {
        e.preventDefault();
        var data = $('#roleform').serialize();
        createRole(data);
        // var url = $('.create-role').attr('href');
        // $.get(url, data, function(xhr){
        // 	if (xhr.status) {location.reload()};
        // },'json');
    });

    $('.edit-role').click(function (e) {
        e.preventDefault();
        var data = $('#roledit').serialize();
        createRole(data);
        // var url = $('.create-role').attr('href');
        // $.get(url, data, function(xhr){
        // 	if (xhr.status) {location.reload()};
        // },'json');
    });

    $('.editrole').click(function () {
        var url = $(this).attr('rel');
        $.get(url, {}, function (xhr) {
            var d = xhr.data;
            var obj = $('#roledit');
            obj.find('input[name="role[description]"]').val(d.description);
            obj.find('input[name="role[name]"]').val(d.name).parents('.form-group').hide();
            obj.find('select[name="role[rule_name]"]').val(d.ruleName);
            obj.find('input[name="role[data]"]').val(d.data);
        }, 'json');
    });

    function createRole(data) {
        var url = $('.create-role').attr('href');
        $.get(url, data, function (xhr) {
            if (xhr.status) {
                location.reload()
            }
        }, 'json');
    }

    $('.action').click(function (e) {
        var action = $(this).val();
        var check = $(this).is(":checked");
        var url = $('.permission').attr('href');
        var des = $(this).parent('span').prev('.action_des').val();
        createpermission(action, des, check);
    });

    $('.action_des').blur(function () {
        var action = $(this).next().find('input').val();
        //console.log(action);
        var des = $(this).val();
        $(this).next().find('input').attr('checked', 'checked');
        createpermission(action, des, true);
    });

    function createpermission(action, des, check) {
        var url = $('.permission').attr('href');
        $.get(url, {permission: action, des: des, check: check}, function (e) {

        }, 'json');
    }


    $('.assign #role-select').change(function () {
        var role = $(this).val();
        assignPermission(role)
    });

    $('.handel').click(function () {
        var rel = $(this).attr('rel');
        var url = $('.assign-permission').attr('href');
        var role = $('.assign #role-select').val();
        var csrf = $('input[name=csrf]').val();
        if (rel == 'add') {
            var val = $('#un').val();
        } else {
            var val = $('#yet').val();
        }

        $.post(url, {method: rel, action: val, _csrf: csrf, role: role}, function (xhr) {
            $('input[name=csrf]').val(xhr.csrf);
            if (xhr.status) {
                assignPermission(role);
            }
        }, 'json');

    });

    function assignPermission(role) {
        var url = $('.permission').attr('href') + '?rolename=' + role;
        $.get(url, null, function (xhr) {
            if (xhr.status) {
                $('#yet').html(xhr.data.yet);
                $('#un').html(xhr.data.un);
            }
        }, 'json');
    }


    $('.assign-permissions').click(function () {
        var id = $(this).data('id');
        var is_sel = $(this).hasClass('selected');
        var _this = this;
        roleAssignPermission(id, is_sel);
    });

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

    $('.user').click(function () {
        var user_id = $(this).data('user_id');
        var is_sel = $(this).hasClass('selected');
        var _this = this;
        roleAssign(user_id, is_sel);
    });

    $('.pinyin').click(function (e) {
        e.preventDefault();
        var user_ids = [];
        $(this).parent().next('.u-list').find('.user:not(.selected)').each(function (e) {
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

    $(function () {
        $('.child #role-select').change(function () {
            var role = $(this).val();
            selectRole(role)
        });

        $('.handel').click(function () {
            var rel = $(this).attr('rel');
            var url = $('.role-child').attr('href');
            var role = $('.child #role-select').val();
            var csrf = $('input[name=csrf]').val();
            if (rel == 'add') {
                var val = $('#other').val();
            } else {
                var val = $('#child').val();
            }
            ;

            $.post(url, {method: rel, _csrf: csrf, child: val, role: role}, function (xhr) {
                $('input[name=csrf]').val(xhr.csrf);
                if (xhr.status) {
                    selectRole(role);
                }
                ;
            }, 'json');
            // $.post(url, {method:rel, child:child, _csrf:csrf, role:role}, function(xhr){
            //     // $('input[name=csrf]').val(xhr.csrf);
            //     // if (xhr.status) {
            //     //     selectRole(role);
            //     // };
            // },'json');

        });
    });

    function selectRole(role) {
        var url = $('.get-child').attr('href') + '?rolename=' + role;
        $.get(url, null, function (xhr) {
            if (xhr.status) {
                $('#child').html(xhr.data.child);
                $('#other').html(xhr.data.other);
            }
            ;
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