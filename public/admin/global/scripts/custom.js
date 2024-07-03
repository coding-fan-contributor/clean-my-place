$(document).ready(function() {
    var table_users = $('#table_users').DataTable({
        "pageLength": 25,
        "order": [[ 0, "desc" ]],
    });
    var table_cleaners = $('#table_cleaners').DataTable({
        "pageLength": 25,
        "order": [[ 0, "desc" ]]
    });
    var table_transactions = $('#table_transactions').DataTable({
        "pageLength": 25,
        "order": [[ 0, "desc" ]]
    });
    var table_reviews = $('#table_reviews').DataTable({
        "pageLength": 25,
        "order": [[ 0, "desc" ]]
    });
    var table_payout = $('#table_payout').DataTable({
        "pageLength": 25,
        "order": [[ 0, "desc" ]]
    });
    var table_orders = $('#table_orders').DataTable({
        "pageLength": 25,
        "order": [[ 0, "desc" ]]
    });

    var table_banners = $('#table_banners').DataTable({
        "pageLength": 25,
        "order": [[ 0, "desc" ]]
    });

    var table_extraservices = $('#table_extraservices').DataTable({
        "order": [[ 0, "desc" ]]
    });

    var table_taxsettings = $('#table_taxsettings').DataTable({
        "order": [[ 0, "desc" ]]
    });



    $(".datepicker").datepicker({
        dateFormat: 'yyyy-mm-dd',
    });


    $("#alert").delay(5000).slideUp(200, function() {
        //closed
    });


    var dataTable = $('.datatableClass').DataTable({
        "order": [[ 0, "asc" ]],
    });
    
});
// functions
// $('#SendPush').click(function(e) {
//     e.preventDefault();
//     let title = $("#title").val();
//     let description = $('#description').val();
//     if (
//         typeof title != "undefined" &&
//         typeof description != "undefined" &&
//         title != "" &&
//         description != ""
//     ) {
//         $.post("/api/sendpush", {
//             title: title,
//             description: description,
//         })
//             .done(function (response) {
//                 swal("Done!", "Push message sent successfully", "success");
//             })
//             .fail(function (xhr, textStatus, errorThrown) {
//                 swal("Error!", "Oops.. Something went wrong", "error");
//             });
//     } else {
//         swal("Oops..", "Title & Description required", "warning");
//     }
// })
/*
 *  Global actions
 */
function delete_item(id, type) {
    swal({
        title: "Are you sure?",
        text: "You will not be able to recover this record!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, delete it!",
        closeOnConfirm: false
    }, function() {
        $.post('/api/delete', {
            id: id,
            type: type
        }).done(function(response) {
            swal("Done!", type + " deleted successfully", "success");
            //$('#table_' + type + 's').DataTable().ajax.reload();
            $('#table_' + type + 's').DataTable().row('#tr_'+id).remove().draw();
        }).fail(function(xhr, textStatus, errorThrown) {
            swal("Error!", "Oops.. Something went wrong", "error");
        });
    });
}

function change_status(id, type) {
    if (typeof type != 'undefined') {
        $.post('/api/changeStatus', {
            id: id,
            type: type
        }).done(function(response) {
            if (response.status == 1) {
                $('#status' + id).text('ACTIVE');
                $('#status' + id).addClass('label-success');
                $('#status' + id).removeClass('label-danger');
                $('#modal_view').addClass('label-success');
                $('#modal_view').removeClass('label-danger');
                $('#modal_view').html('ACTIVE');
            }
            if (response.status == 0) {
                $('#status' + id).text('INACTIVE');
                $('#status' + id).addClass('label-danger');
                $('#status' + id).removeClass('label-success');
                $('#modal_view').addClass('label-danger');
                $('#modal_view').removeClass('label-success');
                $('#modal_view').html('INACTIVE');
            }
            //$('#table_' + type + 's').DataTable().ajax.reload();
        }).fail(function(xhr, textStatus, errorThrown) {
            swal("Error!", "Oops.. Something went wrong", "error");
        });
    }
    else {
        swal("Error!", "Oops.. type required.", "error");
    }
}

function change_payout_status(cleaner_id, amount) {
    if (typeof cleaner_id != 'undefined') {
        $.post('/api/changePayoutStatus', {
            cleaner_id: cleaner_id,
            amount: amount,
            month: $('#month').val(),
            year: $('#year').val(),
        }).done(function(response) {
            if (response.status == 'paid') {
                $('#status' + cleaner_id).text('Paid');
                $('#status' + cleaner_id).addClass('label-success');
                $('#status' + cleaner_id).removeClass('label-default');
            }
            if (response.status == 'pending') {
                $('#status' + cleaner_id).text('pending');
                $('#status' + cleaner_id).addClass('label-default');
                $('#status' + cleaner_id).removeClass('label-success');
            }
            //$('#table_' + type + 's').DataTable().ajax.reload();
        }).fail(function(xhr, textStatus, errorThrown) {
            swal("Error!", "Oops.. Something went wrong", "error");
        });
    }
    else {
        swal("Error!", "Oops.. cleaner id required.", "error");
    }
}


$("#Select_all").click(function() {
    $('input:checkbox').not(this).prop('checked', this.checked);
});

function Multi_Delete(type) {
    if (typeof type == 'undefined') {
        return swal("Error!", "type error.", "error");
    }
    var items = [];
    $.each($("input:checked"), function() {
        items.push($(this).val());
    });
    //alert("My favourite sports are: " + items.join(", "));
    swal({
        title: "Are you sure?",
        text: "You will not be able to recover these records!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, delete it!",
        closeOnConfirm: false
    }, function() {
        $.post('/api/multi_delete', {
            ids: items,
            type: type
        }).done(function(response) {
            swal("Success!", "Records deleted successfully", "success");
            $('#table_' + type + 's').DataTable().ajax.reload();
        }).fail(function(xhr, textStatus, errorThrown) {
            swal("Error!", "Oops.. Something went wrong", "error");
        });
    });
}

// form validations =======================
$("#UpdateUserForm").validate({
    rules: {
        name: {
            required: true
        },
        address: {
            //required: true
        },
        username: {
            required: true,
        },
        email: {
            required: true,
            email: true
        },
        password: {
            //required: true,
        },
    },
    messages: {
        name: {
            required: "This is a required field"
        },
    },
    errorPlacement: function(error, element) {
        var name = $(element).attr("name");
        error.appendTo($("#" + name + "_validate"));
    },
});
$("#CreateUserForm").validate({
    rules: {
        name: {
            required: true
        },
        address: {
            required: true
        },
        username: {
            required: true,
        },
        email: {
            required: true,
            email: true
        },
        password: {
            required: true,
        },
    },
    messages: {
        name: {
            required: "This is a required field"
        }
    },
    errorPlacement: function(error, element) {
        var name = $(element).attr("name");
        error.appendTo($("#" + name + "_validate"));
    },
});
$("#AddProductForm").validate({
    rules: {
        name: {
            required: true
        },
        species: {
            required: true
        },
        point: {
            required: true
        },
        image1: {
            required: true
        },
        caption1: {
            required: true
        },
        information: {
            required: true
        },
    },
    messages: {
        name: {
            required: "This is a required field"
        }
    },
    errorPlacement: function(error, element) {
        var name = $(element).attr("name");
        error.appendTo($("#" + name + "_validate"));
    },
});
$("#UpdateProductForm").validate({
    rules: {
        name: {
            required: true
        },
        species: {
            required: true
        },
        point: {
            required: true
        },
        caption1: {
            required: true
        },
        information: {
            required: true
        },
    },
    messages: {
        name: {
            required: "This is a required field"
        }
    },
    errorPlacement: function(error, element) {
        var name = $(element).attr("name");
        error.appendTo($("#" + name + "_validate"));
    },
});
$("#AddProducttypeForm").validate({
    rules: {
        name: {
            required: true
        },
    },
    messages: {
        name: {
            required: "This is a required field"
        }
    },
    errorPlacement: function(error, element) {
        var name = $(element).attr("name");
        error.appendTo($("#" + name + "_validate"));
    },
});
$("#AddAdForm").validate({
    rules: {
        name: {
            required: true
        },
        ad_image: {
            required: true
        },
        priority: {
            required: true
        },
        description: {
            required: true
        }
    },
    messages: {
        name: {
            required: "This is a required field"
        }
    },
    errorPlacement: function(error, element) {
        var name = $(element).attr("name");
        error.appendTo($("#" + name + "_validate"));
    },
});
$("#UpdateAdForm").validate({
    rules: {
        name: {
            required: true
        },
        priority: {
            required: true
        },
        description: {
            required: true
        }
    },
    messages: {
        name: {
            required: "This is a required field"
        }
    },
    errorPlacement: function(error, element) {
        var name = $(element).attr("name");
        error.appendTo($("#" + name + "_validate"));
    },
});
$("#AddMilestoneForm").validate({
    rules: {
        type: {
            required: true
        },
        point: {
            required: true
        },
        value: {
            required: true
        }
    },
    messages: {
        name: {
            required: "This is a required field"
        }
    },
    errorPlacement: function(error, element) {
        var name = $(element).attr("name");
        error.appendTo($("#" + name + "_validate"));
    },
});

$("#AddCategoryForm").validate({
    rules: {
        name: {
            required: true
        },
        point: {
            required: true
        }
    },
    messages: {
        name: {
            required: "This is a required field"
        }
    },
    errorPlacement: function(error, element) {
        var name = $(element).attr("name");
        error.appendTo($("#" + name + "_validate"));
    },
});

$("#AddQuestionForm").validate({
    rules: {
        name: {
            required: true
        },
        category: {
            required: true
        },
        option1: {
            required: true
        },
        option2: {
            required: true
        },
        option3: {
            required: true
        },
        option4: {
            required: true
        }
    },
    messages: {
        name: {
            required: "This is a required field"
        }
    },
    errorPlacement: function(error, element) {
        var name = $(element).attr("name");
        error.appendTo($("#" + name + "_validate"));
    },
});

// $("#PushNotificationForm").validate({
//     rules: {
//         name: {
//             required: true
//         },
//         description: {
//             required: true
//         }
//     },
//     messages: {
//         name: {
//             required: "This is a required field"
//         }
//     },
//     errorPlacement: function(error, element) {
//         var name = $(element).attr("name");
//         error.appendTo($("#" + name + "_validate"));
//     },
// });

// custom functions


// =============================
function viewModal(data){
    //console.log(data);
    $('.message').html(data);
    $('#myModal').modal('show');
}


$(document).ready(function(){
    tinymce.init({
        selector:'textarea',
        //plugins: 'placeholder print preview fullpage searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern help',
        toolbar: 'formatselect | bold italic strikethrough forecolor backcolor | link image media pageembed | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent | removeformat | addcomment',

         plugins: 'placeholder lists',
        // toolbar: 'numlist bullist',
    });

});

// export csv
$('#export_users').click(function(e){
    e.preventDefault();
    window.open('/admin/export_users', '_blank');
});
$('#export_cleaners').click(function(e){
    e.preventDefault();
    window.open('/admin/export_cleaners', '_blank');
});
$('#export_orders').click(function(e){
    e.preventDefault();
    let start = $('#start').val();
    let end = $('#end').val();
    if(start != '' && end != ''){
        window.open('/admin/export_orders/'+start+'/'+end, '_blank');
    }else{
        window.open('/admin/export_orders', '_blank');
    }
});

$('#export_transactions').click(function(e){
    e.preventDefault();
    let start = $('#start').val();
    let end = $('#end').val();
    if(start != '' && end != ''){
        window.open('/admin/export_transactions/'+start+'/'+end, '_blank');
    }else{
        window.open('/admin/export_transactions', '_blank');
    }
});


function change_cleans_status(id){
    if (id) {
        $.post('/api/changeOrderHistoryStatus', {
            id: id
        }).done(function(response) {
            if (response.status == 'paid') {
                $('#history_status' + id).text('Paid');
                $('#history_status' + id).addClass('label-success');
                $('#history_status' + id).removeClass('label-default');
            }
            if (response.status == 'pending') {
                $('#history_status' + id).text('Pending');
                $('#history_status' + id).addClass('label-default');
                $('#history_status' + id).removeClass('label-success');
            }
            if (response.status == 'completed') {
                $('#history_status' + id).text('Completed');
                $('#history_status' + id).addClass('label-success');
                $('#history_status' + id).removeClass('label-default');
            }
            //$('#table_' + type + 's').DataTable().ajax.reload();
        }).fail(function(xhr, textStatus, errorThrown) {
            swal("Error!", "Oops.. Something went wrong", "error");
        });
    }
    else {
        swal("Error!", "Oops.. id required.", "error");
    }
}

function change_availability(id){
    if (id) {
        $.post('/api/changeAvailability', {
            id: id
        }).done(function(response) {
            if (response.available == 'yes') {
                $('#status_availability_' + id).text('yes');
                $('#status_availability_' + id).addClass('label-success');
                $('#status_availability_' + id).removeClass('label-danger');
            }
            if (response.available == 'no') {
                $('#status_availability_' + id).text('no');
                $('#status_availability_' + id).addClass('label-danger');
                $('#status_availability_' + id).removeClass('label-success');
            }
            //$('#table_' + type + 's').DataTable().ajax.reload();
        }).fail(function(xhr, textStatus, errorThrown) {
            swal("Error!", "Oops.. Something went wrong", "error");
        });
    }
    else {
        swal("Error!", "Oops.. id required.", "error");
    }
}


function remove_btn(id, cleaner_id){
    $.ajax({
        'url':'/api/deletecleanerschedule',
        'method':'post',
        'data':{'id': id, 'cleaner_id': cleaner_id},
        'success':function( success){
            $('#schedule_'+id).remove();
        }
    });
}

$('#generateSchedule').on('click', function(){
    var start = $('select[name="start"]').val();
    var end = $('select[name="end"]').val();
    if(start >= end){
        swal("Oops..", " Start time should be smaller then End time.", "error");
        return false;
    }
});

// default datatable
$('.datatable').DataTable({
    "order": [[ 0, "desc" ]]
});


// pay payout new
function Pay(cleaner_id, amount) {
    var months = {'01':'January', '02':'February', '03':'March', '04':'April', '05':'May', '06':'June', '07':'July', '08':'August', '09':'September', '10':'October', '11':'November', '12':'December'};
    var month = $('#month').val();
    var monthName = months[month];
    var start = $('#start').val();
    var end = $('#end').val();
    if(start == '' || end == ''){
        var message = 'You are paying for all pending payments of '+monthName+' month';
    }else{
        var message = 'You are paying for the pending payments of '+monthName+' month, starting from '+start+' and ends on '+end;
    }
    if (typeof cleaner_id != 'undefined') {
        swal({
            title: "Are you sure?",
            text: message,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, mark as paid!",
            closeOnConfirm: false
        }, function() {
            $.post('/api/pay_payout', {
                cleaner_id: cleaner_id,
                amount: amount,
                month: $('#month').val(),
                year: $('#year').val(),
                start: $('#start').val(),
                end: $('#end').val(),
            }).done(function(response) {
                swal("Done!", "Paid successfully", "success");
                $('#tr_' + cleaner_id).remove();
                //$('#table_' + type + 's').DataTable().ajax.reload();
            }).fail(function(xhr, textStatus, errorThrown) {
                swal("Error!", "Oops.. Something went wrong", "error");
            });
        });
        
    }
    else {
        swal("Error!", "Oops.. cleaner id required.", "error");
    }
}


function changeDate(id, date) {
    console.log(id, date);

    swal(
        {
            title: "Change Date",
            text: "Write new date:",
            type: "input",
            showCancelButton: true,
            closeOnConfirm: false,
            inputPlaceholder: "dd-mm-yyyy",
            inputValue: date,
        },
        function (inputValue) {
            if (inputValue === false) return false;
            if (inputValue === "" || inputValue.length < 10) {
                swal.showInputError("You need to write date properly!");
                return false;
            }
            $.post("/api/changehistorydate", {
                id: id,
                date: inputValue,
            })
                .done(function (response) {
                    $("#date_" + id).text(inputValue);
                    swal("Done!", "New date is : " + inputValue, "success");
                })
                .fail(function (xhr, textStatus, errorThrown) {
                    swal("Error!", "Oops.. Something went wrong", "error");
                });
        }
    );
}


// resend notifications
function resend_notifications(order_id){
    $.post("/api/resend_notifications", {
        order_id: order_id,
    })
    .done(function (response) {
        swal("Done!", response.message, "success");
        setTimeout(function(){ location.reload(); }, 3000);
    })
    .fail(function (xhr, textStatus, errorThrown) {
        swal("Error!", xhr.responseJSON.error, "error");
    });
}


// update priority
function updatePriority(id, type, priority){
    if(typeof id != 'undefined' && typeof type != 'undefined'){
        $.post("/api/updatePriority", {
            id: id,
            type: type,
            priority: priority,
        });
    } 
}
