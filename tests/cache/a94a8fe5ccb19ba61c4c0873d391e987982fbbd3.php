<?php $name = "Franck"; $lastname = "Dakia"; ?>
<?php
    $name = 'Tintin';
?>

@raw
    if ($name == 'Tintin') {
        <?php echo $name; ?>
    }
@endraw

<?php echo $name; ?> <?php echo $lastname; ?>

<?php echo e("<div>".$name."</div>"); ?>


<?php if ($name == "Franck"): ?>

    <?php echo $name; ?>

<?php endif; ?>

<?php foreach ([$name, $lastname] as $name): ?>

    <?php echo $name; ?>

<?php endforeach; ?>