$(function(){
  // edit row
  $('body').on('click', 'td[class^=edit-row-]', function() {
    if ($(this).text() == 'edit') {
      toEdit($(this));
    } else {
      toSave($(this));
    };
  });

  function toEdit(toEdit) {
    $(toEdit).parent('tr').addClass('edit');
    copyUserData();
    $(toEdit).text('save');
  }

  function toSave(toSave) {
    replaceInputs();
    updateDB();
    $(toSave).text('edit');
    $(toSave).parent('tr').removeClass('edit');
  }

  function copyUserData() {
    $('tr.edit td').each(function() {
      if (!$(this).hasClass('primary') && $(this).attr('data-column')) {
        var tdVal = $(this).text();
        var tdDataColumn = $(this).attr('data-column');
        var cellWidth = $(this).width()-4;
        var cellHeight = $(this).height()-4;
        $(this).empty();
        if (tdVal.length <= 255) {
          var addInput = '<input type="text" value="'+tdVal+'" style="width:'+cellWidth+'px;" />';
        } else {
          var addInput = '<textarea style="width:'+cellWidth+'px;height:'+cellHeight+'px;">'+tdVal+'</textarea>';
        };

        $(this).append(addInput);
      };
    });
  };

  function replaceInputs() {
    $('tr.edit td input').each(function() {
      var inputVal = $(this).val();
      $(this).parent('td').append(inputVal);
      $(this).remove();
    });
  };

  function updateDB() {
    // all vals
    var updatedField = [];
    $('tr.edit td[data-column]').each(function() {
      updatedField.push($(this).text());
    });

    // db column names
    var colName = [];
    $('tr.head th').each(function() {
      colName.push($(this).text());
    });

    if (updatedField.length == colName.length) {
      var info = {};
      info.list = [];
      for (var i = 0; i < updatedField.length; i++) {
        info.list.push[info[colName[i]] = updatedField[i]];
      }
    };

    var pathname = window.location.href.split('?')[0];

    // update database
    $.ajax({
      type: "post",
      url: pathname+'tpls/db.update.php',
      data: info,
      success: function(data) {
        if (data) {
          //alert(data);
          $('body').prepend(data);
        } else {
          console.log('so far, so good!');
        };
      },
      error: function(XMLHttpRequest, textStatus, errorThrown, data) {
        alert('Something went wrong, please try again later.');
      }
    });
  };

  // delete row confirm
  $('body').on('click', 'td[class^=delete]', function() {
    $(this).parent('tr').toggleClass('delete');

    // launch warning
    var modalBack = '<div id="modal"></div><div id="modal-holder"><h1>Do you really want to remove this row?</h1><button id="yes-remove">YES</button><button id="no-remove">NO</button></div>';
    $('body').append(modalBack);
  });

  // delete row confirm
  function removeRow() {


    // all vals
    var updatedField = [];
    $('tr.delete td[data-column]').each(function() {
      updatedField.push($(this).text());
    });

    // db column names
    var colName = [];
    $('tr.head th').each(function() {
      colName.push($(this).text());
    });

    if (updatedField.length == colName.length) {
      var info = {};
      info.list = [];
      for (var i = 0; i < updatedField.length; i++) {
        info.list.push[info[colName[i]] = updatedField[i]];
      }
    };

    var pathname = window.location.href.split('?')[0];
    // delete row
    $.ajax({
      type: "post",
      url: pathname+'tpls/remove.php',
      data: info,
      success: function(data) {
        if (data) {
          $('body').prepend(data);
        } else {
          $('tr.delete').remove();
        };
      },
      error: function(XMLHttpRequest, textStatus, errorThrown, data) {
        alert('Something went wrong, please try again later.');
      }
    });
  };

  // remove modal
  function removeModal() {
    $('#modal').remove();
    $('#modal-holder').remove();
  };

  $('body').on('click', '#no-remove', function() {
    removeModal();
    $('tr.delete').removeClass('delete');
  });

  $('body').on('click', '#yes-remove', function() {
    removeModal();
    removeRow();
  });
});
