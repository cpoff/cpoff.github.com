
<?=$messages?>

<div class="row product-single">

    <div class="col-sm-9 col-sm-push-3">
        
        <div class="widget" style="margin-bottom: 0">
        	<h6 class="title">Search Catalog</h6>
            <hr>
            
        <form <?=$search_attributes?>>

            <div class="form-group input-group">
                <span class="input-group-btn" title="Search">
                    <button type="submit" name="<?=$catalog_page['id']?>_submit" style="width:69px" class="btn btn-primary btn-thin">
                        <i style="font-size:16px" class="ti-search"></i>
                    </button>
                </span>

                <input type="search" name="<?=$catalog_page['id']?>_query"
                    placeholder="Search">
            </div>

        </form>
            
        </div>

            

        <div class="row">

            <div class="col-sm-6">
                <?php // The image class allows the attribute system to change the image. image-url allows us to hide the image in the code area of the product as needed ?>
                <img src="<?=h($image_url)?>" class="image img-responsive center-block image-url">
                
                <div class="code"><?=$code?></div>
            </div>

            <div class="col-sm-6">
                
                <div class="description">
                  
                <h4 class="short_description uppercase"><?=h($short_description)?></h4>

                <div class="full_description"><?=$full_description?></div>

                <div class="mb32 mb-xs-24 number price"><?=$price_info?></div>
                    
                <hr class="mb24">
                 
                <?php
                    // If there are products that are allowed to be added to the cart,
                    // then start the form.
                    if ($available_products):
                ?>
                    <form <?=$form_attributes?>>
                <?php endif ?>

                <?php
                    // Loop through the product attributes in order to output
                    // a row for each attribute.
                    foreach($attributes as $attribute):
                ?>

                    <div class="attribute_<?=$attribute['id']?> attribute_row form-group">

                        <label for="attribute_<?=$attribute['id']?>">
                            <?=h($attribute['label'])?>
                        </label>

                        <?php
                            // The "width: 100%" fixes a Bootstrap issue where the
                            // select was not 100% width when the clear button was hidden.
                        ?>
                        <div class="input-group" style="width: 100%">
                            <div class="select-option">
    							<i class="ti-angle-down"></i>
                            	<select name="attribute_<?=$attribute['id']?>" id="attribute_<?=$attribute['id']?>"></select>
                            </div>

                            <span class="clear input-group-btn" title="Clear">
                        		<button type="button" class="btn btn-secondary btn-thin" style="width:69px; border-width:1px" >
                            		<i class="ti-close" style="font-size:16px"></i>
                        		</button>
                    		</span>
                         
                        </div>

                    </div>

                <?php endforeach ?>

                <?php
                    // The product row is shown when a product group is being shown,
                    // there is at least one product in that product group, and
                    // there are no attributes.
                    if ($product_row):
                ?>

                    <?php
                        // If there are available products and there is more than 1 product
                        // then show pick list of products to allow customer to select product.
                        if ($product_pick_list):
                    ?>

                        <div class="form-group">
                            <label for="product_id">Item</label>
                            <select name="product_id" id="product_id"></select>
                        </div>

                    <?php
                        // Otherwise there are no available products or there is just 1
                        // available product, so just output text list of product(s).
                        else:
                    ?>

                        <?php
                            // If there is only 1 product, then just output that 1 product.
                            if (count($products) == 1):
                        ?>

                            <p><strong>Item:</strong> <?=$products[0]['description']?></p>

                        <?php
                            // Otherwise there are multiple products, so output a list of them.
                            else:
                        ?>

                            <p><strong>Items:</strong></p>

                            <ul>
                                <?php foreach($products as $product): ?>
                                    <li><?=$product['description']?></li>
                                <?php endforeach ?>
                            </ul>

                        <?php endif ?>

                    <?php endif ?>

                <?php endif ?>

                <?php
                    // If there are products that are allowed to be added to the cart,
                    // then output recipient & quantity rows, buttons, and close form.
                    if ($available_products):
                ?>

                        <?php if ($recipient): ?>

                            <div class="row">
                                <div class="col-md-6">
                        			<div class="form-group">
                                		<label for="ship_to">Ship to</label>
                                			<div class="select-option">
												<i class="ti-angle-down"></i>
                                				<select name="ship_to" id="ship_to"></select>
                                			</div>
                            		</div>
                            	</div>

                        		<div class="col-md-6">
                            		<div class="form-group">
                                		<label for="add_name">or add name</label>
                                			<input type="text" name="add_name" id="add_name" placeholder="Example: &quot;Mom&quot;">
                            		</div>
                        		</div>
                        	</div>

                        <?php endif ?>

                        <div class="form-group add-to-cart">
                            
                        <?php if ($quantity): ?>
                                <input type="number" name="quantity" id="quantity" value="1" min="1" max="999999999" placeholder="QTY">
                        <?php endif ?>

                  		<button type="submit" class="btn btn-primary" style="margin-bottom:0"><?=h($add_button_label)?></button> 

                        </div>

                        <?=$system // Required hidden fields and JS (do not remove) ?>

                    </form>

                <?php
                    // Otherwise, there are no products that are allowed to be
                    // added to the cart, so just output the back button, if necessary.
                    else:
                ?>

                    <?php if ($back_button_url): ?>
                        <div class="form-group">
                            <a href="<?=h($back_button_url)?>" class="btn btn-default btn-secondary">
                                <?=h($back_button_label)?>
                            </a>
                        </div>
                    <?php endif ?>

                <?php endif ?>

                </div>
            </div>

        </div>

        <div class="details mt32"><?=$details?></div>

        <?=
            // JS for attributes. Must be placed below all elements that are
            // updated by attribute system (e.g. details, code). (do not remove)
            $footer_system
        ?>

    </div>

    <div class="col-sm-3 col-sm-pull-9">
 
        <div class="widget">
        	<h6 class="title">Browse Catalog</h6>
           	<hr>

        <nav>
            <ul class="nav nav-stacked link-list">
                <?php foreach($product_groups as $product_group): ?>
                    <li
                        <?php if ($product_group['current']): ?>
                            class="active"
                        <?php endif ?>
                    >
                        <a href="<?=h($product_group['url'])?>"
                            <?php if ($product_group['level']): ?>
                                style="padding-left: <?=(2*$product_group['level'])?>em"
                            <?php endif ?>
                        >
                            <?=h($product_group['name'])?>
                        </a>
                    </li>
                <?php endforeach ?>
            </ul>
        </nav>
            
        </div>

        <?php if ($keywords): ?>
        	<div class="widget">
        		<h6 class="title">Keywords</h6>
           		<hr>
					<?php foreach($keywords as $keyword): ?>
                		<a href="<?=h($keyword['url'])?>"><?=h($keyword['keyword'])?></a>
                	<?php endforeach ?>
        	</div>
        <?php endif ?>

        <?php if ($currency): ?>

            <form <?=$currency_attributes?>>

                <div class="form-group">
                	<div class="select-option">
						<i class="ti-angle-down"></i>
                    	<select name="currency_id" id="currency_id"></select>
                    </div>
                </div>

                <?=$currency_system // Required hidden fields and JS (do not remove) ?>

            </form>

        <?php endif ?>
              
    </div>

</div>
