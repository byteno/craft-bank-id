/**
 * BankID plugin for Craft CMS
 *
 * Index Field JS
 *
 * @author    Byte AS
 * @copyright Copyright (c) 2019 Byte AS
 * @link      https://byte.no
 * @package   BankID
 * @since     1.0.0
 */

function copyUrl()

{
    let text = document.getElementById("copy-url").innerHTML;
    let textField = document.createElement('textarea');
    textField.innerText = text;
    document.body.appendChild(textField);
    textField.select();
    document.execCommand('copy');
    textField.remove();

    let modal = document.getElementById("modal");
    modal.style.display = "block";

    setTimeout(function(){
        modal.style.display = "none";
    }, 1000)

}
