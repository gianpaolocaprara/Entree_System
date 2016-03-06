<?php
	header("Content-type: text/css");
	include("Config.php");
?>

body{
	background-color: <?php echo $config["aspetto"]["col_sfondo"]; ?>;
    color: <?php echo $config["aspetto"]["col_testo"]; ?>;
    font-family: <?php echo $config["aspetto"]["font"]; ?>;
   	background-image:  <?php echo $config["aspetto"]["immagine"]; ?>;
}

#intestazione{
	color: <?php echo $config["aspetto"]["col_sfondo"]; ?>;
    background-color: <?php echo $config["aspetto"]["col_intestazione"]; ?>;

}

#menu{
	color: <?php echo $config["aspetto"]["col_sfondo"]; ?>;
    background-color: <?php echo $config["aspetto"]["col_menu"]; ?>;
    float: left;
    display: block;
    width: 100%;
}

#menu a {
	margin: 5px;
	color: <?php echo $config["aspetto"]["col_sfondo"]; ?>;
    font-weight: bold;
}

.post{
	border: thin dashed <?php echo $config["aspetto"]["col_menu"]; ?>;
    padding: 5px;
}

#blog{
	width: 600px;
}

a { color: blue;
} 

a:hover { text-decoration: underline;
	color: red;
	}