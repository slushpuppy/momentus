/*!
 * Copyright (c) 2013 Smart IO Labs
 * Project repository: http://smartiolabs.com
 * license: Is not allowed to use any part of the code.
 */
var $ = jQuery;

$(document).ready(function() {
  $('#smio-submit').click(function(){
    var form = $(this).parents('form');
    if(!validateForm(form)) {
      alert("Check Form Entries/Add Author Name");
      return false;
    }

  });
  if($('#smpubap_rewprov').length > 0){
    $('#smpubap_rewprov').change(function(){
      $(".smpubap_rewprov_options").hide();
      $(".smpubap_rewprov_"+$(this).val()).show();
      $("#smpubap_rewprov_link").attr("href", $(".smpubap_rewprov_"+$("#smpubap_rewprov").val()+":first").attr("data-url"));
    });
    $("#smpubap_rewprov_link").attr("href", $(".smpubap_rewprov_"+$("#smpubap_rewprov").val()+":first").attr("data-url"));
  }
  $('#push-token-list td span').click(function(){
    $(this).attr('style', 'height:auto');
  });
  $('.pluginers-applytoall').click(function(event){
    if(!confirm("Action will be applied to all results, continue?")){
      event.preventDefault();
      return;
    }
  });
  $('.smio-delete').click(function(event){
    var confirmtxt = $(this).attr("data-confirm");
    if(typeof confirmtxt == "undefined"){
      confirmtxt = "Are you sure you want to continue?";
    }
    if (!confirm(confirmtxt)){
      event.preventDefault();
    }
  });
  if($('.pluginers_date').length > 0){
    $('.pluginers_date').dateRangePicker();
  }
});

function pluginers_delete_service(id){
  if(!confirm("Are you sure you want to continue?")){
    return;
  }
  $('.pluginers_service_'+id+'_loading').show();
  $.get(pluginers_pageurl, {'noheader':1, 'delete': 1, 'id': id}
  ,function(data){
    $('.pluginers_service_'+id+'_loading').hide();
    $('#pluginers-service-tab-'+id).hide(600, function() {
      $('#pluginers-service-tab-'+id).remove("push-alternate");
    });
  });
}

function pluginers_open_service(id, actiontype, action){
  if(actiontype == 1){
    if(confirm("Do you want to save current changes?")){
      $('#pluginers_jform').ajaxSubmit();
    }
  }
  else if(actiontype == 2){
    $(".pluginers-canhide").hide();
    $("#col-left").attr("style", "width:30%");
  }
  $(".pluginers_form_ajax").show();
  $('.pluginers-service-tab').removeClass("push-alternate");
  $('#pluginers-service-tab-'+id).addClass("push-alternate");
  $('.pluginers_service_'+id+'_loading').show();

  $.get(pluginers_pageurl, {'noheader':1, 'action': action, 'id': id}
  ,function(data){
        $('.pluginers_service_'+id+'_loading').hide();
    $('.pluginers_form_ajax').html(data);
    var pluginers_form_options = {
        beforeSubmit:  function(){$('.pluginers_process').show()},
        success:       function(responseText, statusText){
          $(".pluginers_process").hide();
          $('.pluginers_service_'+id+'_loading').hide();
          if(responseText != 1){
            alert(JSON.stringify(responseText['message']));
            $('body').scrollTop(0);
          }
          else{

            $(".pluginers_form_ajax").fadeOut("fast", function(){
              $('.pluginers_form_ajax').html('');
              if(actiontype == 2){
                $("#col-left").attr("style", "width:100%");
                $(".pluginers-canhide").show();
                location.reload();
              }
              if(id != -1){

                $("html, body").animate({scrollTop: $('#pluginers-service-tab-'+id).offset().top-100}, "slow");
              }
            });
          }
        }
    };
    
    $('#pluginers_jform').ajaxForm(pluginers_form_options);
    
    $('#smio-submit').click(function(){
      var form = $(this).parents('form');
      if (!validateForm(form)) return false;
    });
    
    $('#pluginers_jform .pluginers_toggle').change(function(){
      $(this).find("option").each(function(){
        var element = ".pluginers_toggle_"+$(this).val();
          $(element).hide();
      });
      var element = ".pluginers_toggle_"+$(this).val();
      $(element).show();
    });
    
    $('#pluginers_jform input[name="smart_grabber"]').change(function(){
      if($('#pluginers_jform input[name="smart_grabber"]:checked').length){
        $(".grabberFields").show();
      }
      else{
        $(".grabberFields").hide();
      }
    });
    $('#pluginers_jform input[name="smart_grabber"]').trigger("change");
    
    if($('#pluginers_jform .pluginers_country_autocomplete').length > 0){
      $("#pluginers_jform input[name='jscountry']").change(function(){if($(this).val() == "")$("#pluginers_jform input[name='country']").val("")});
      var options = {
        url: pluginers_jsurl+"/countries.json",
        getValue: "name",
        list: {
          match: {
            enabled: true
          },
          onSelectItemEvent: function() {
              var value = $("#pluginers_jform input[name='jscountry']").getSelectedItemData().code;
              $("#pluginers_jform input[name='country']").val(value).trigger("change");
          }
        }
      };
      $('#pluginers_jform .pluginers_country_autocomplete').easyAutocomplete(options);
    }
    if($('#pluginers_jform .pluginers_lang_autocomplete').length > 0){
      $("#pluginers_jform input[name='jslang']").change(function(){if($(this).val() == "")$("#pluginers_jform input[name='lang']").val("")});
      var options = {
        url: pluginers_jsurl+"/languages.json",
        getValue: "name",
        list: {
          match: {
            enabled: true
          },
          onSelectItemEvent: function() {
              var value = $("#pluginers_jform input[name='jslang']").getSelectedItemData().code;
              $("#pluginers_jform input[name='lang']").val(value).trigger("change");
          }
        }
      };
      $('#pluginers_jform .pluginers_lang_autocomplete').easyAutocomplete(options);
    }
    if($('#pluginers_jform .pluginers_date').length > 0){
      $('#pluginers_jform .pluginers_date').dateRangePicker();
    }
    $('.pluginers_service_'+id+'_loading').hide();
    if(id != -1)$("html, body").animate({scrollTop: 0}, "slow");
    $(".pluginers_form_ajax .checkonoff").switchButton({
      width: 50,
      height: 20,
      button_width: 25,
      show_labels: false
    });
  });
}

function pluginersRssDelRow(button) {
  if($(".pluginersReplaceWords div").length == 1){
    return;
  }
  $(button).closest("div").remove();
}

function pluginersRssAddRow(button) {
  var newRow = "<div class='pluginers-clear'>"+$(button).closest("div").html()+"</div>";
  $(".pluginersReplaceWords").append(newRow);
  $(".pluginersReplaceWords div:last").find("select").val("");
  $(".pluginersReplaceWords div:last").find("input[type='text']").val("");
}

function smpubapPostType(select){
  $('.pluginers_service_-1_loading').show();
  $.get(pluginers_pageurl+"&loadtaxs=1&noheader=1&smiopub_post_type="+$(select).val(), function(data){
    $('.pluginers_service_-1_loading').hide();
    $("#smpubapPostTaxSelc").html(data);
  });
}

function smpubapPostTax(select){
  $('.pluginers_service_-1_loading').show();
  $.get(pluginers_pageurl+"&loadcats=1&noheader=1&smiopub_object_name="+$(select).val(), function(data){
    $('.pluginers_service_-1_loading').hide();
    $(".smpubapPostTaxDIV").html(data);
  });
}

$('body').on('click', '.accordion', function() {
  $(this).toggleClass("active");
  var panel = $(this).next()[0];
  if (panel.style.display === "block") {
    panel.style.display = "none";

  } else {
    panel.style.display = "block";
  }
});