<?php

/**
 * Class mpandcoOrder_completed
 */
class mpandcoOrder_completed {

    /**
     * @var WC_Order
     */
    private $order;

    /**
     * @var bool
     */
    private $metaDataAvailable;

    /**
     * @var mixed
     */
    private $PaymentResponse;

    /**
     * Order_completed constructor.
     * @param WC_Order $order
     */
    public function __construct(WC_Order $order ) {
		$this->order = $order;
		$this->PaymentResponse = false;
		$this->metaDataAvailable = false;
		$this->PaymentResponse = $this->order->get_meta('PaymentSaleResponse_mPandco');
		if ($this->PaymentResponse)
        {
            $this->PaymentResponse = unserialize($this->PaymentResponse);
            $this->metaDataAvailable = true;
        }
	}

    /**
     *
     */
    public function render( ) {
		?>
		<div class="row row-mpandco">
            <div class="col-mpandco">
                <div class="alert alert-info">
                    <h2 class="entry-title title-mpandco">
			            <?php
			            echo __('Orden completada','mpandco');
			            ?>
                    </h2>
                    <p class="subtitle subtitle-mpandco">
			            <?php
			            echo __('El proceso de pago no se realizo debido a que la orden se encuentra previamente completada','mpandco');
			            ?>
                    </p>
                </div>
            </div>
		</div>
        <?php
            if ($this->metaDataAvailable)
            {
                ?>
                <script type="text/javascript">
                    function growDiv(ID) {
                        let growDiv = document.getElementById(ID);
                        if (growDiv.clientHeight) {
                            growDiv.style.height = '0px';
                        } else {
                            let wrapper = document.querySelector('#'+ID + ' .transaction-content');
                            growDiv.style.height = wrapper.clientHeight + "px";
                        }
                    }
                </script>

                <div class="row row-mpandco">
                    <div class="col-12 col-mpandco">
                        <h3>
				            <?php
				            echo __('Detalles de la transacción de pago','mpandco')
				            ?>
                        </h3>
                        <div class="card-mpandco">
                            <div class="card-content">
                                <div class="row row-mpandco">
                                    <div class="col-mpandco">
                                        <div class="icon_mpandco">
                                            <img src="<?php echo plugins_url('/assets/img/mpandco_logo_payment_gateway.png',__DIR__) ?>" alt="mPandco" style="max-width: 150px; float: right">
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-mpandco">
                                    <div class="col col-6">
                                        <label class="label-mpancdo">
                                            <span>
                                                <?php echo __('Referencia intención de pago: ','mpandco')?>
                                            </span>
                                            <?php echo $this->PaymentResponse['id']?>
                                        </label>
                                    </div>
                                    <div class="col col-sm-6">
                                        <label class="label-mpancdo">
                                            <span>
                                                <?php echo __('Estado: ','mpandco')?>
                                            </span>
			                                <?php echo $this->PaymentResponse['state']?>
                                        </label>
                                    </div>
                                </div>
                                <hr class="line-mpandco">
                                <p class="card-mpandco-subtitle"><?php echo __('Total:','mpandco').' '.$this->PaymentResponse['total']['currency']['id'] ?></p>
                                <table>
                                    <thead>
                                    <tr>
                                        <th><?php echo __('Items','mpandco')?></th>
                                        <th><?php echo __('Envio','mpandco')?></th>
                                        <th><?php echo __('Tax','mpandco')?></th>
                                        <th><?php echo __('Monto','mpandco')?></th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <tr>
                                        <td>
                                            <?php echo $this->PaymentResponse['total']['items']?>
                                        </td>
                                        <td>
		                                    <?php echo $this->PaymentResponse['total']['shipping']?>
                                        </td>
                                        <td>
		                                    <?php echo $this->PaymentResponse['total']['tax']?>
                                        </td>
                                        <td>
		                                    <?php echo $this->PaymentResponse['total']['amount']?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <p class="card-mpandco-subtitle"><?php echo __('Transacciones: ','mpandco') ?></p>
                                <?php
                                    foreach ($this->PaymentResponse['transaction'] as $transation){
                                        ?>
                                        <div class="mpandco-box-transaction">
                                            <p class="mpandco-box-transaction-title">
                                                <span><?php echo __('Transacción: ','mpandco')?></span>
                                                <?php echo $transation['related_resources']['sale']['ref']?>
                                            </p>
                                            <div class="mpandco-icon-expand" onclick="growDiv('<?php echo $transation['related_resources']['sale']['ref']?>')">
                                                <img src="<?php echo plugins_url('/assets/img/arrow_drop_down_white.png',__DIR__) ?>" alt="mPandco">
                                            </div>
                                            <div class="mpandco-box-transaction-content" id="<?php echo $transation['related_resources']['sale']['ref']?>">
                                                <div class="transaction-content row-mpandco" style="justify-content: space-between" >
                                                    <div class="col">
                                                        <label class="label-mpancdo">
                                                            <span>
                                                                <?php echo __('Total: ','mpandco')?>
                                                            </span>
                                                            <?php echo $transation['amount']['total'].' '.$transation['amount']['currency']['id']?>
                                                        </label>
                                                    </div>
                                                    <div class="col">
                                                        <label class="label-mpancdo">
			                                                <?php echo $transation['related_resources']['sale']['_data']['type_payment_label']?>
                                                        </label>
                                                    </div>
                                                    <div class="col">
                                                        <span class="mpandco-badge" style="background-color:<?php
                                                            echo $transation['related_resources']['sale']['_data']['summary_status_color']
                                                        ?>;"> <?php echo $transation['related_resources']['sale']['_data']['status'] ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
	}
}