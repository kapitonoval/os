<style>
.field_for_fill{
 cursor:pointer;
 background-color: #E6DCC8;
}
</style>
<div class="agreement">
     <div class="cap" id="agreement_tools_plank">
        <table width="100%" border="0">
          <tr>
            <td width="400"><!--width="167"-->
                <?php 
                    if(isset($_GET['conrtol_num']))
                    {
                    // это срабатывает когда осуществляется переход из шага выбора договора для создания спецификации, через ссылку напротив договора
                ?>
                    <button type="button" onclick="location = '/admin/order_manager/?page=agreement&section=choice&client_id=<?php echo $client_id; ?>&conrtol_num=<?php echo $_GET['conrtol_num']; ?>';" style="cursor:pointer;">закрыть</button>
                    <!--
                    <button type="button" onclick="location = '/admin/order_manager/?page=clients&razdel=show_client_folder&sub_razdel=agreements&client_id=<?php echo $client_id; ?>';" style="cursor:pointer;">вернуться в Договоры</button>
                    <button type="button" onclick="location = '/admin/order_manager/?page=clients&razdel=show_client_folder&sub_razdel=calculate_table&client_id=<?php echo $client_id; ?>';" style="cursor:pointer;">вернуться в РТ</button>-->
                <?php 
                    }
                    else
                    {
                ?>
                     <button type="button" onclick="location = '/admin/order_manager/?page=clients&razdel=show_client_folder&sub_razdel=calculate_table&client_id=<?php echo $client_id; ?>';" style="cursor:pointer;">вернуться в РТ</button>
                     <button type="button" onclick="location = '/admin/order_manager/?page=clients&razdel=show_client_folder&sub_razdel=agreements&client_id=<?php echo $client_id; ?>';" style="cursor:pointer;">вернуться в Договоры</button>
        
                <?php 
                    }
                ?>  
            </td>
            <td>
                <?php echo 'Договор №'.$agreement['agreement_num'].' от '.$agreement_date.' ('.fetchOneValFromAgreementTbl(array('retrieve' => 'type_ru','coll' => 'type' ,'val' => $_GET['agreement_type'])).')'; ?>
            </td>
            <td>  
             
            </td>
            <td align="right">
                <?php 
                    if(isset($_GET['open']) && $_GET['open']== 'specification')
                    {
                ?>
                
                   <button type="button" onclick="location = '/admin/order_manager/?<?php echo addOrReplaceGetOnURL('section=specification_full_editor'); ?>';" style="cursor:pointer;">редактировать текст СП</button>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                   <button type="button" onclick=" location = location.pathname + '?page=agreement&section=specification_editor&client_id=<?php echo $client_id; ?>&specification_num=<?php echo $key; ?>&agreement_id=<?php echo $agreement_id; ?>' ;" style="cursor:pointer;">редактировать СП</button>
                 <?php 
                    }
                    else{
            
                        // if($user_status=='1' && ((boolean)$agreement['standart'])){
                       if((boolean)$agreement['standart']){
                  ?>
                        <button type="button" onclick="location = '/admin/order_manager/?<?php echo addOrReplaceGetOnURL('section=agreement_full_editor'); ?>';" style="cursor:pointer;">редактировать договор</button>
                <?php 
                        }
                    }
                    if(((boolean)$agreement['standart']) || (isset($_GET['open']) && $_GET['open'] == 'specification')){
                ?>
                <button type="button" onclick="conv_specification.start();print_agreement();" style="cursor:pointer;">распечатать</button>&nbsp;&nbsp;&nbsp;&nbsp;
                <?php 
                    }
                 ?>
            </td>
          </tr>
        </table>
     </div>
     <div class="agreement_field" id="agreement_blank">
    <!-- <?php //print_r($_GET); ?><br /><br />
     <?php //print_r($our_firm); ?><br /><br />
     <?php //print_r($client_firm); ?><br /><br />
    --> 
     <?php echo $agreement_content; ?>
     <?php echo $specifications; ?>
     </div>
</div>
<script type="text/javascript" src="libs/js/textRedactor.js"></script>
<script type="text/javascript" src="libs/js/geometry.js"></script>
<script type="text/javascript">
   textRedactor.install(['?page=agreement&update_agreement_finally_sheet_ajax=1','?page=agreement&update_specification_common_fields_ajax=1']);
</script>