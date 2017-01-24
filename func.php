<?php

class ViewPages
{
	public static function bannerPrint()
	{
        echo  "<table width=800' border='1' align='center' cellpadding='0' cellspacing='0'>"
             ."<tr><td align='center' class='style1'><img src='img/banner.jpg' width='800' height='123'></td></tr>"
             ."<tr><td align='center'><table width='100%' height='500' border='0' cellspacing='0' cellpadding='0'><tr>";
	}
	
	public static function MenuPrint() 
	{
		$html = "<td width='22%' valign='top' bgcolor='#E8EAEC'>"
               ."<p align='center' class='title'>Меню</p>"
               ."<div id='coolmenu'>"
               ."<a href='index.php'>Заявки</a>";
		
		
		if ($_SESSION['type_id'] == 2) {
			$html .= "<a href='appsview.php' id='myHref1'>Новая заявка</a>";
			
		}
		$html .= "<a href=\"logout.php"."\">Выход</a></div></td>";
		
        echo  $html;
	}


    public static function footerPrint() 
	{
        print("</table></td></tr><tr>");
        print("<td height='28' align='center' bgcolor='#CCCCCC'><I>Система заявок на ремонт</I></td></tr></table>");
        print("<tr><td align='center'><table width='100%' border='0' cellspacing='0' cellpadding='0'><tr>");
    }
	
}

?>