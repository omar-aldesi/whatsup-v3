<?php
header("Content-Type: text/css");

    $primaryColor = getValidColor($_GET['primary_color'] ?? '#7F56D9');
    $secondaryColor = getValidColor($_GET['secondary_color'] ?? '#ff8');

    function getValidColor($color): bool|string
    {
        $color = '#' . ltrim($color, '#'); // Ensure that the color has a leading hash
        return preg_match('/^#[a-f0-9]{6}$/i', $color) ? $color : false; // Check if the color is valid, otherwise return false
    }

    if (!$primaryColor) {
        $primaryColor = '#6009f0'; // Use default color if primary color is not valid
    }

    if (!$secondaryColor) {
        $secondaryColor = '#8105f0'; // Use default color if secondary color is not valid
    }
?>

    .btn--primary::after, .btn--primary, .btn--primary-outline:hover::after, .btn--primary-outline::after, .custom-accordion .accordion-button:not(.collapsed), .custom-accordion .accordion-button:not(.collapsed)::after{
        background: linear-gradient(-75deg, <?php echo $primaryColor ?> 31%, <?php echo $secondaryColor ?> 98%);
    }

   .btn--primary-outline, .banner-action-item, .banner-action-item:hover, .nav-menu-item a:is(.menu-item-active), .btn--white, .btn--white-outline:hover{
        color: <?php echo $primaryColor ?>;
    }


    .btn--primary-outline{
        border: 1px solid <?php echo $primaryColor ?>;
    }

   .custom-accordion .accordion-button:not(.collapsed),.custom-accordion .accordion-button:not(.collapsed)::after{
        background: <?php echo $primaryColor ?>;
        color:#fff;
    }
