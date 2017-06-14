<div class="col-xs-12 col-md-12">
    <div class="Lemonway_payment_form">
        <form class="placeOrderForm" id="LemonWay_payment_form" action="<?php echo $continue; ?>" method="POST">
            <input type="hidden" value="CC" name="method_code">
            <div class="radio">
                <label>
                    <div class="radio"><span><input type="radio" name="cc_type" value="CB" required></span></div>
                    <img alt="CB" src="catalog/view/theme/default/image/LemonWay-CB.gif">
                </label>
            </div>
            <div class="radio">
                <label>
                    <div class="radio"><span><input type="radio" name="cc_type" value="VISA" required></span></div>
                    <img alt="VISA" src="catalog/view/theme/default/image/LemonWay-VISA.gif">
                </label>
            </div>
            <div class="radio">
                <label>
                    <div class="radio"><span><input type="radio" name="cc_type" value="MASTERCARD" required></span>
                    </div>
                    <img alt="MASTERCARD" src="catalog/view/theme/default/image/LemonWay-MASTERCARD.gif">
                </label>
            </div>


            <?php  if($lemonway_oneclick_enabled=='1'){
            ?>
            <?php if (!empty($card['card_type'])){
            ?>
            <script>
                // Pour débloquer
                // if($("input[name$='cc_type']" ).val( )==$card['card_type'])
                $("input[name='cc_type']").each(function () {
                    if ($(this).val() == '<?php echo $card['card_type'];?>'
                    )
                    {
                        $(this).prop("checked", true);
                    }

                })


            </script>

            <?php

            }

            ?>

            <!-- Oneclic form -->
            <div class="lemonway-payment-container" id="lemonway_CC_payment_form_container">
                <div class="lemonway-payment-img-container">
                    <img class="lemonway-payment-icon img-responsive"
                         src="catalog/view/theme/default/image/LemonWay-paiement-mode.png" width="500px"
                         alt="Credit card" id="payment-lemonway-CC-logo">
                </div>
                <h3 class="lemonway-method-title"><?php echo $text_card; ?> </h3>

                <?php if (empty($card['card_num'])) { ?>
                <div class="lemonway-payment-oneclic-container">
                    <!-- User can choose to save his card -->
                    <div class="checkbox">
                        <label for="lw_register_card">
                            <div class="checker" id="uniform-lw_register_card">
                                <span>
                                    <input id="lw_register_card"  value="register_card"   type="checkbox"   name="lemonway_oneclic">
                                </span>
                            </div>
                            <?php echo $entry_save_card;?>
                        </label>
                    </div>
                </div>
                <?php
                  }
                  else{
                ?>

                <!-- User already have a card. He can choose to use it or not-->
                <div>
                    <div class="radio">
                        <label for="lw_use_card"> <input id="lw_use_card" value="use_card" checked type="radio"   name="lemonway_oneclic" checked/> <?php echo $entry_use_card; ?>
                        </label>
                    </div>
                </div>
                <div>
                    <!-- Card Number -->
                    <label> <?php echo $entry_actual_card;?>  <span> <?php echo $card['card_num']; ?> </span></label>
                </div>
                <?php
                if (!empty($card['card_exp'])){
                 ?>

                <!-- Exp Date  -->
                <div>
                    <label> <?php echo htmlspecialchars($entry_expiration_date.$card['card_exp']) ;?> </label>
                </div>

                <?php
                 }
                 ?>


                <div>
                    <div class="radio">
                        <label for="lw_register_card"> <input id="lw_register_card"  value="register_card" type="radio"  name="lemonway_oneclic"/> <?php echo $entry_save_new_card;?>
                        </label>
                    </div>


                    <div class="radio">
                        <label for="lw_no_use_card"> <input id="lw_no_use_card"    type="radio" name="lemonway_oneclic" value="no_use_card"/> <?php echo $entry_not_use_card; ?>
                        </label>
                    </div>

                </div>
                <br/>
            </div>


            <?php
                }
               }//One Click Enabled
              ?>


            <div class="buttons">
                <div class="pull-right">
                    <button type="submit" form="LemonWay_payment_form" data-toggle="tooltip" class="btn btn-primary"
                            data-loading-text="<?php echo $text_loading; ?>">
                        <!--   <a href="<?php echo $continue; ?>"> <?php echo $button_continue; ?></a>  -->
                        <?php echo $button_continue; ?>
                    </button>

                </div>
            </div>


        </form>
    </div>
</div>
