function char_random() {
 var obj=document.color_form;
 var elem=obj.string.value;
 var separator="";
 var exit_str="";
 var colors=new Array(
 "00","11","22","33","44","55","66","77","88","99","AA","BB","CC","DD","EE","FF");
 elem=elem.split(obj.whois_rand.options[obj.whois_rand.selectedIndex].value);

 for (var n=0;n<=(elem.length-1);n++) {
  if (elem[n]==" ") { exit_str+=" "; }
  if (elem[n]!=" ") {
    separator=obj.whois_rand.options[obj.whois_rand.selectedIndex].value;
    var col1=Math.round(Math.random()*(colors.length-1));
    var col2=Math.round(Math.random()*(colors.length-1));
    var col3=Math.round(Math.random()*(colors.length-1));
    var size=Math.round(Math.random()*4)+3;
    exit_str+="<font color=\"#"+colors[col1]+colors[col2]+colors[col3]+
    "\" size=\""+size+"\">"+elem[n]+"</font>"+separator+"";
  }
 }
 obj.res_code.value=exit_str;
 result.innerHTML=exit_str;
}
char_random();