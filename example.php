<?php
require 'EDictionary.php';
$dict = new EDictionary();  // initialize dictionary
?>
<h3>translate word 'move' from English into Russian using AbbyyLingvo system</h3>
<p><?= $dict->translate('move', 'en_ru', 'abbyyLingvo'); ?></p>

<h3>translate word 'move' from English into Russian using Yandex.Dictionary system</h3>
<p><?= $dict->translate('move', 'en_ru', 'yandexDict'); ?></p>

<h3>translate word 'двигаться' from Russian into English using AbbyyLingvo system</h3>
<p><?= $dict->translate('двигаться', 'ru_en', new ETAdapterAbbyyLingvo()); ?></p>

<h3>translate word 'двигаться' from Russian into English using Yandex.Dictionary system</h3>
<p><?= $dict->translate('двигаться', 'ru_en',  new ETAdapterYandexDict()); ?></p>


<h3>translate word 'glass' from Chinese into English using AbbyyLingvo system</h3>
<p><?= $dict->translate('玻璃', 'zhCN_en', 'abbyyLingvo'); ?></p>

<h3>Yandex.Dictionary system does not support translating from Chinese into English</h3>
<p>
<?php
try {
    $dict->translate('玻璃', 'zhCN_en', 'yandexDict');
} catch(Exception $e) {
    echo $e->getMessage();
}
?>
</p>
