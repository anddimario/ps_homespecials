<!-- MODULE Block specials 2, edited by Nemo to hook in center column -->
<div>
	<h4><center>{l s='Offerte' mod='homespecials'} <a href="{$link->getPageLink('prices-drop.php')}" title="{l s='See all' mod='blockspecials'}">{l s='Vedi tutti' mod='homespecials'}</a></center></h4>
	{if isset($special) AND $special}
	<div class="grid-container">
			{foreach from=$special item=product name=homeSpecialProducts}
		<div class="grid-item">
			<div class="products">
				<div class="thumbnail-container">
					<a href="{$product.link}" title="{$product.name|escape:html:'UTF-8'}" class="product_image"><img src="{$link->getImageLink($product.link, $product.id_product, 'medium_default')}" height="{$homeSize.height}" width="{$homeSize.width}" alt="{$product.name|escape:html:'UTF-8'}" />{if isset($product.new) && $product.new == 1}<span class="new">{l s='New'}</span>{/if}</a>
				</div>
				<div class="product-description">
					<h5><a href="{$product.link}" title="{$product.name|truncate:50:'...'|escape:'htmlall':'UTF-8'}">{$product.name|truncate:35:'...'|escape:'htmlall':'UTF-8'}</a></h5>
					<div class="product_desc"><a href="{$product.link}" title="{l s='More' mod='homespecials'}">{$product.description_short|strip_tags|truncate:65:'...'}</a></div>
					<s>{$product.price_without_reduction} €</s> <font color="red">{$product.price} €</font>
						
				</div>
			</div>
		</div>
			{/foreach}
		</div>
	{else}
		<p>{l s='No special price products' mod='homespecials'}</p>
	{/if}
</div>
<!-- /MODULE Block specials -->