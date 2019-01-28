var countData=0;
var dataSet='';
var datalength=5
var startdata=0
var prenumber=0
var continueprosses=false
jQuery( document ).ready(function($) {
  jQuery('#xml_upload_file').change(function(){
    jQuery('#xml_upload').submit()
  })




  // jQuery('#output').on('click','.img-title',function(){
    
  //   jQuery('.img-title').removeClass("active");
  //   if( jQuery(this).next('.expand-ul').is(':visible')){
  //     jQuery(this).next('.expand-ul').hide();
  //     jQuery(this).removeClass("active");
  //   }else{
  //     jQuery(this).next('.expand-ul').show();
  //     jQuery(this).addClass("active");
  //   }
    
  // })



  jQuery('#xml_upload').submit(function(){
    var formData = new FormData($(this)[0]);
    formData.append( 'action', 'uploadXML');
    $.ajax({
      url : ajax_object.ajaxurl,
      type: "POST",
      action: "uploadXML",
      data: formData,
      contentType: false,
      processData:false,
      beforeSend: function() {
        resetProgressBar()
      },
      complete: function() {
          
      },
      xhr: function(){
          //upload Progress
        var xhr = $.ajaxSettings.xhr();
        if (xhr.upload) {
            xhr.upload.addEventListener('progress', function(event) {
                console.log(event)
                var percent = 0;
                var position = event.loaded || event.position;
                var total = event.total;
                if (event.lengthComputable) {
                    percent = Math.ceil(position / total * 100);
                }
                startProgressBar(percent,'File Uploading...')
               
            }, true);
        }
        return xhr;
      },
      mimeType:"multipart/form-data"
  }).done(function(res){ 
    var obj = JSON.parse(res);
    $('#xml_upload')[0].reset(); //reset form
    $('.submit_btn').val("Upload").prop( "disabled", false); 
    console.log(obj)
    countData=obj.uploads.length;
    dataSet=obj.uploads;
    ajaXuploads();
    /*$('#output').jstree({ 'core' : {
        'data' : obj.data    
      }
    });*/
  });

  return false
  })
});

function ajaXuploads(){
  var pr=Math.round((100/countData)*datalength)
  //console.log(pr) 
  //console.log(prenumber)
  var getnumberofdata = dataSet.sort(function(a, b) { return a.Variable1 < b.Variable1 ? 1 : -1; }).slice(startdata, datalength+startdata);
  //console.log(getnumberofdata)
  startdata=datalength+startdata
  jQuery.ajax({
    url : ajax_object.ajaxurl,
    type: "POST",
    action: "uploadImg",
    data: {'action':'uploadImg','data':getnumberofdata},
    success: function (response) { 
    startProgressBar(pr,'Image Uploading...');
    continueprosses=true
    var obj = JSON.parse(response);
    //alert(obj);
    var str='';
    for (var key in obj.print) {
        if(typeof obj.print[key] === 'object'){
          var ob=obj.print[key];
           str+='<div class="main-title">'+ob['title']+'</div>';
          delete ob.title;
           for (var key1 in ob) {
            if(typeof ob[key1] === 'object'){
              str+='<div class="img-title">'+key1+'<span>&#x27A7;</span></div><div class="expand-ul">';
              if(ob[key1].url!=''){
                str+='<ul>';
                str+='<li>image</li>';
                str+='<li><a href="'+ob[key1].url+'" target="_blank">Link</a></li>';
                str+='</ul>';
              }
              if(ob[key1].height!=='null'){
                str+='<ul>';
                str+='<li>height</li>';
                str+='<li>'+ob[key1].height+'</li>';
                str+='</ul>';
              }
              if(ob[key1].width!=='null'){
                str+='<ul>';
                str+='<li>width</li>';
                str+='<li>'+ob[key1].width+'</li>';
                str+='</ul>';
              }
               str+='</div>';
           }
         }
        }
    }
    jQuery('#output').append(str)
      if(startdata>countData){
      }else{
        ajaXuploads()
      }
    },error: function(jqXHR, textStatus, errorThrown) {
       console.log(textStatus, errorThrown);
    }   
  })
}

function resetProgressBar(){
  jQuery('.meter').show()
  jQuery('.meter > span').addClass('notransition')
  jQuery('.meter > span').css('width','0')
  prenumber=0;
}

function startProgressBar(pr,text){
  if(!continueprosses){
    resetProgressBar()
  }
  jQuery('.meter-text').text(text)
  jQuery('.meter').show('slow')
  jQuery('.meter > span').removeClass('notransition')
  if(prenumber<100){
    for(var i=prenumber;i<=pr+prenumber;i++){
        jQuery('.meter > span').css('width',i+'%')
        jQuery('.showper').text(i+'%')
    }
  }
  prenumber=pr+prenumber;
}



    


