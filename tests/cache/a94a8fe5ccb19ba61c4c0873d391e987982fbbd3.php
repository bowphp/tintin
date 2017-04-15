<?php $name = "Franck"; $lastname = "Dakia"; ?>
<?php echo $name; ?> <?php echo $lastname; ?>
<?php echo e("<div>".$name."</div>"); ?>

<?php if ($name == "Franck"): ?>
    <?php echo $name; ?>
<?php endif; ?>
<?php foreach ([$name, $lastname] as $name): ?>
    <?php if (($name == "Franck")): continue; endif;?>
    <?php if (($name == "Franck")): break; endif;?>
    <?php echo $name; ?>
<?php endforeach; ?>
<?php $i = -10; ?>

<?php while ($i < 0): ?>
    <?php $i++; ?>
<?php endwhile; ?>