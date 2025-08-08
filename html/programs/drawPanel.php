function getImg(f,m,b,t, time){
    f=("00000000000" + f).slice(-11);
    var s=f;
    f = s.replace(/^\s*0+/, ({ length }) => ' '.repeat(length));
    var mode=m;
    var bw=b;
    var tx=t;
    const canvas = document.getElementById('myCanvas');
    ctx = canvas.getContext('2d');
    ctx.rect(200, 0, 400, 150);
    ctx.fillStyle = "orange";
    ctx.fillRect(0, 0, 400, 150);     
    ctx.font = "20px 'Helvetica'";
    ctx.fillStyle = ["rgb(",100,",",100,",",100,")"].join("");
    ctx.fillText("MODE: "+ mode,10,120);
    ctx.fillText("BANDWIDTH: "+ bw,10,140);
   ctx.fillText("UTC: "+ time,180,25);
    if (t==1){
        ctx.fillStyle = ["rgb(",256,",",0,",",0,")"].join("");
        ctx.fillText("TRANSMIT",180,120);
    }else{
//        ctx.fillText("RECEIVE",180,120);
   }
    const img = new Image();
    img.onload = () => {
      var digitWidth =23; // Width of one digit in the image
      var periodWidth = 10;
      var digitHeight =28; // Height of one digit in the image
     // Iterate over each character in the string
      var periods=0;
      var sWidth=0;
      var x="";
      var digit=0;
      var dx=25;
      var sWidth=digitWidth;
      for (let i = 0; i < f.length; i++) {
          if (f[i].indexOf(".")>-1){
              digit=10;
              sWidth = 6;
               dx=i*digitWidth;
               console.log("period: " + dx);
               periods=periods+1;
          }else{
                digit = parseInt(f[i]);
                dx=i*digitWidth ;
               sWidth = 44;
            } 
         const sHeight = digitHeight;

        // Calculate source rectangle (assuming digits are arranged horizontally)
          sx = (digit * (44));
        var sy = 10; // Assuming all digits are in the first row

        // Calculate destination rectangle on canvas
        var dy =  20; // Keep the same vertical position
        var dWidth=44;
        var tOK=1;
        if (f[i].indexOf('0')>-1){
         f[i]="9";
         }
        if (f[i].indexOf(".")>-1){
            dWidth = 10;
        }else{
            dWidth = 44;
        }
        const dHeight = 35;

        // Draw the part of the image
         ctx.fillStyle = "black";
         ctx.fillStyle = ["rgb(",100,",",100,",",100,")"].join("");

        ctx.fillText("MAIN",10,25);
        ctx.drawImage(img, sx, sy, sWidth, sHeight, dx+10, 2*dy-15, dWidth, 40);
      }
    };

    img.src = "/Images/wide-dots1.png"; // Set the source of the image
    };
 