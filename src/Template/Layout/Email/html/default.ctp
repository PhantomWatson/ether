<?php
/**
 * @var \App\View\AppView $this
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    </head>
    <body>
        <table cellspacing="0" cellpadding="0" border="0" width="100%" style="font-family: Verdana, Helvetica, Arial;">
            <tr>
                <td bgcolor="#FFFFFF" align="center">
                    <table width="650px" cellspacing="0" cellpadding="3">
                        <tr>
                            <td style="color: #777777; font-size: 130%; padding-bottom: 20px;">
                                A message from Ether, your friendly neighborhood thought repository...
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?= $this->fetch('content') ?>

                                <p style="margin-top: 50px;">
                                    <span style="font-size: 100%;">
                                        Love,
                                    </span>
                                    <br />
                                    <span style="font-size: 160%; font-weight: bold;">
                                        Ether
                                    </span>
                                    <br />
                                    <a href="http://theEther.com/" style="font-size: 80%;">
                                        http://theEther.com/
                                    </a>
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>