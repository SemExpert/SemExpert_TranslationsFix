# Translations Fix

Magento 2 translations fail to load theme translations which makes it impossible to customize translations for a given 
theme without changing the installed language pack.

**Translations Fix** implements the fix proposed in 
https://github.com/magento/magento2/issues/8508#issuecomment-279332346 by changing the load preference for the affected
model to a fixed version that loads the necessary design translations.