
 <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>

 <?php 
extract($displayData);
$docuemnt = \JFactory::getDocument();
$docuemnt->addStyleSheet(JURI::root() . 'media/plg_jdsociallogin/assets/css/fields-style.css');
?>
<div class="social-container"></div>
<input type="hidden" name="<?php echo $name; ?>" id="social-<?php echo $id; ?>" value="">

<script>
    (function($){
        $(function(){
                
    var container = $('.social-container');
    $("#social-<?php echo $id; ?>").val("<?php echo $value ?>");

  container.delegate('.box','mouseenter mouseout',handleMouse);
  container.sortable();
  
          function handleMouse(e) {
            if (e.type == "mouseenter") {
              $("#social-<?php echo $id; ?>").val($(".box").attr("sn"));
            }
            else if (e.type == "mouseout") {
              $("#social-<?php echo $id; ?>").val($(".box").attr("sn"));
            }
          }
            
            <?php if(empty($value)){ ?>
                var socialNetworks = [{"name":"facebook"},{"name":"twitter"}];
            <?php }else{ ?>
            
                <?php if($value =="facebook") { ?>
                var socialNetworks = [{"name":"facebook"},{"name":"twitter"}];
                <?php } else{ ?>
                var socialNetworks = [{"name":"twitter"},{"name":"facebook"}];
                <?php } ?>
            
            <?php } ?>
            $.each(socialNetworks,function(key,item){
                var box = $('<div class="box"></div>');
                box.attr('id',key);
                box.attr('sn',item.name);
                box.addClass(item.name);
                if(item.name=="facebook"){
                    var imgUrl  = "<?php echo  JURI::root() . 'media/plg_jdsociallogin/assets/img/facebook.png'; ?>";
                    box.html('<img src="'+imgUrl+'" height="60" alt="Facebook"> <h2>facebook</h2>');
                }else if(item.name=="twitter"){
                  var imgUrl  = "<?php  echo JURI::root() . 'media/plg_jdsociallogin/assets/img/twitter.png'; ?>";
                    box.html('<img src="'+imgUrl+'" height="60" alt="Twitter"> <h2>Twitter</h2>');
                }
                box.sortable();
                container.append(box);
         
            });
        })
    })(jQuery);

</script>