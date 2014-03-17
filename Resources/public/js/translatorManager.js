translatorManager = function(options){
	this._initialize();

}

translatorManager.instance = null;

translatorManager.getInstance = function (){
	if(translatorManager.instance == null)
		translatorManager.instance = new translatorManager();
	return translatorManager.instance;
}

translatorManager.prototype = {
    _initialize: function(){
        this._toggle = false;
        this._selected_key = null;
    },
    
    sendGetTranslations: function(form)
    {
        var self = this;
        self.removeEditorOpenInstances();
        self._selected_key = null;
        var my_waiting_noty = callNoty("Procesando", "information");
        $.ajax({
          url: $(form).attr('action'),
          data: $(form).serialize(),
          type: $(form).attr('method'),
          dataType: 'json',
          success: function(json){
              if(json.status == "OK")
              {
                $("#bundles_container_error").html("");
                $("#bundles_texts_container").html(json.options.html);
              }
              else
              {
                $("#bundles_container_error").html(json.options.message);
                $("#bundles_texts_container").html("");
              }
          }, 
          complete: function()
          {
            my_waiting_noty.close();
          }
        });
        return false;
    },
    
    sendPutTranslations: function(form)
    {
        var my_waiting_noty = callNoty("Procesando", "information");
        $.ajax({
          url: $(form).attr('action'),
          data: $(form).serialize(),
          type: $(form).attr('method'),
          dataType: 'json',
          success: function(json){
              if(json.status == "OK")
              {
                callNoty("Cambios guardados correctamente", 'success');
              }
              else
              {
                callNoty("Errores al guardar", 'alert');
              }
          }, 
          complete: function()
          {
            my_waiting_noty.close();
          }
              
        });
        return false;
    },
    
    doChanges: function(myUrl)
    {
        var my_waiting_noty = callNoty("Procesando", "information");
        $.ajax({
          url: myUrl,
          type: 'get',
          dataType: 'json',
          success: function(json){
              if(json.status == "OK")
              {
                callNoty("Cambios aplicados correctamente", 'success');
              }
          }, 
          complete: function()
          {
            my_waiting_noty.close();
          }
              
        });
        return false;
    },
    
    removeEditorOpenInstances: function()
    {
      var self = this;
      if(self._selected_key != null)
      {
        //console.info('removing old instance of tinymce');
        $('#'+self._selected_key+'_translation textarea').tinymce().remove();
      }
    },
    
    doShowHide: function (key)
    {
      $('#'+key+'_translation').toggle();
      var self = this;
      translatorManager.getInstance().removeEditorOpenInstances();      
      self._selected_key = key;
      $(".translation_container_data").each(function(index, data){
          if(data.id != "tranlation_container_data_"+key)
          {
            if(!self._toggle)
            {
              $(data).hide();
            }
            else
            {
              $(data).show();
            }
            
          }
      });
      self._toggle = ! self._toggle;
      
      var tinyOptions = {
          force_p_newlines: false,
          force_br_newlines : false,
          forced_root_block : '',
          plugins: [
              "advlist autolink lists link image charmap print preview anchor",
              "searchreplace visualblocks code fullscreen",
              "insertdatetime media table contextmenu paste"
          ],
          toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
      };
      //console.info(tinyOptions);
      $('#'+key+'_translation textarea').tinymce(tinyOptions);
    }
}


function callNoty(message, status)
{
  var timeout = 1000;
  var noty_id = noty({
    text: message,
    layout: 'center',
    timeout: timeout,
    type: status
  });
  return noty_id;
}