### Custom Product Builder for Magento2 by Buildateam
[![pipeline status](https://git.buildateam.io/mage/m2-custom-product-builder/badges/master/pipeline.svg)](https://git.buildateam.io/mage/m2-custom-product-builder/commits/master)

## Installation Instructions via Composer. 

1) Add new repository into composer.json wher located extension
2) run `composer require buildateam/m2-custom-product-builder:dev-master`
3) run `php bin:magento setup:upgrade`
4) run `php bin/magento setupe:di:compile`
4) run `php bin/magento setup:static-content:deploy`

## Products Import/Export
1) Export ```
curl -k -X GET https://magento.thecustomproductbuilder.com/customproductbuilder/product/export/id/1 > ~/Downloads/prod1.json
```
2) Import ```
curl -X POST --data @<(cat 3.json) http://cpb.loc2/customproductbuilder/product/import/id/3
```

https://magento.thecustomproductbuilder.com/customproductbuilder/product/export/id/3
https://magento.thecustomproductbuilder.com/customproductbuilder/product/export/id/2
https://magento.thecustomproductbuilder.com/customproductbuilder/product/export/id/1