<?php

/**
 * @param $attributes
 * @param \App\Models\MerchantProduct $product
 * @return array
 */
function AddProductAttributes($attributes, \App\Models\MerchantProduct $product){
    foreach($attributes as $key=>$attribute){
        $required = $attribute['required'];
        if($key=='required')
            continue;
        $AllAttributes = [];
        if((!key_exists('val',$attribute)) || ((isset($attribute['val'])) && ($attribute['val']===null))){
            $productAttribute = [
                'val' => null,
            ];
            $AllAttributes[] = AddOneProAttribute($key, $required, $productAttribute, $product);
        } else {
            for ($x = 0; $x < count($attribute['val']); $x++) {
                $productAttribute = [
                            'val'           => $attribute['val'][$x],
                            'stock'         => ((isset($attribute['stock'][$x])) ? $attribute['stock'][$x] : 0),
                            'qty'           => $attribute['qty'][$x],
                            'pricetype'     => $attribute['pricetype'][$x],
                            'price'         => $attribute['price'][$x]
                ];
                $AllAttributes[] = AddOneProAttribute($key, $required, $productAttribute, $product);
            }
        }
    }
    return $AllAttributes;
}


/**
 * @param $attributes
 * @param \App\Models\MerchantProduct $product
 * @return array
 */
function UpdateProductAttributes($attributes, \App\Models\MerchantProduct $product){
    $oldattr = $product->attribute;
    foreach($oldattr as $oneattr){
        $oneattr->delete();
    }
    $AllAttributes = AddProductAttributes($attributes,$product);
    return $AllAttributes;
}


/**
 * @param $attributeId
 * @param $required
 * @param $productAttribute
 * @param \App\Models\MerchantProduct $product
 * @return $this|\Illuminate\Database\Eloquent\Model
 */
function AddOneProAttribute($attributeId, $required, $productAttribute, \App\Models\MerchantProduct $product,$ProAttrubite=false){
    $ProAttArr = [
        'attribute_id'                  =>  $attributeId,
        'required'                      =>  $required,
        'selected_attribute_value'      =>  ((isset($productAttribute['val']))?$productAttribute['val']:null),
        'stock'                         =>  ((isset($productAttribute['stock']))?$productAttribute['stock']:0),
        'quantity'                      =>  ((isset($productAttribute['qty']))?$productAttribute['qty']:null),
    ];

    if(isset($productAttribute['price']))
        $ProAttArr['plus_price'] = (($productAttribute['pricetype']=='-')?(-1*abs($productAttribute['price'])):$productAttribute['price']);
    if($ProAttrubite)
        return $ProAttrubite->update($ProAttArr);
    else {
        if(!$product->attribute()->where('selected_attribute_value','=',$ProAttArr['selected_attribute_value'])->first()){
            return $product->attribute()->create($ProAttArr);
        }
    }
}
