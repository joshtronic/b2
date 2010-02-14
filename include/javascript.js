function 
PopUp(url, height, width)
{
  open(url, "pop-up", "height=" + height + ",width=" + width + ",scrollbars=yes");
}

function 
Confirm(question)
{
  temp = window.confirm(question);
  window.status=(temp)?'confirm: true':'confirm: false';
  return(temp);
}
