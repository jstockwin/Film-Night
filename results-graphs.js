function drawRunOffGraph(t,r,n){"use strict";var e={x1:0,y1:.5,x2:1,y2:.5,x:1,y:1},s=10,o=100,i=100,a='<svg id="graph" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox=" 0 0 '+i+" "+(s+(s+o)*n.length)+'">',l="",h=[],g=0,f=0,u=0;for(x=0;x<t.length;x++)u+=r[t[x]];for(var x=0;x<t.length;x++){var d=i*r[t[x]]/u;h.push(g);var v='<rect x="'+g+'" y="'+f+'" width="'+d+'" height="'+s+'" fill="'+nodeColors[x%nodeColors.length]+'"/>';g+=d,l+=v}for(f=s,x=0;x<n.length;x++){g=0;for(var w=[],y=0;y<t.length;y++)for(var b=0;b<t.length;b++)if(n[x][t[b]]&&n[x][t[b]][t[y]]){var p=g-h[b],c=i*n[x][t[b]][t[y]]/u,m='<path opacity="0.8" stroke-width="'+c+'" stroke="'+nodeColors[b%nodeColors.length]+'" fill="none" d="M'+(h[b]+c/2)+" "+f+" c "+e.x1*p+" "+e.y1*o+" "+e.x2*p+" "+e.y2*s+" "+p+" "+o+'"/>';g+=c,h[b]=h[b]+c,l+=m,w[y]=w[y]?w[y]+n[x][t[b]][t[y]]:n[x][t[b]][t[y]]}for(h=[],f+=o,g=0,y=0;y<t.length;y++){d=w[y]?i*w[y]/u:0,h.push(g);var v='<rect x="'+g+'" y="'+f+'" width="'+d+'" height="'+s+'" fill="'+nodeColors[y%nodeColors.length]+'"/>';g+=d,l+=v}}return f+=s,a+l+"</svg>"}function drawDirectedGraph(t,r){"use strict";for(var n="1B28BF",e="BF1B1B",s=100,o=10,i=10,a=s/i,l='<svg id="graph" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="'+(-s-o)+" "+(-s-o)+" "+(2*s+2*o+3*a+25)+" "+(2*s+2*o)+'" style="overflow: visible">',h="",g="<defs>",f=[],u=0;u<t.length;u++){var x=s*Math.sin(-u/t.length*2*Math.PI+Math.PI),d=s*Math.cos(-u/t.length*2*Math.PI+Math.PI);f.push({x:x,y:d})}var v=Number.POSITIVE_INFINITY,w=0;for(u=0;u<t.length;u++)for(var y=0;y<t.length;y++)r[u][y]>=r[y][u]&&u!==y&&(v=Math.min(v,r[u][y]),w=Math.max(w,r[u][y]));for(u=0;u<t.length;u++)for(var y=0;y<t.length;y++)if(r[u][y]<r[y][u]){var b=f[u].x-f[y].x,p=f[u].y-f[y].y,c=Math.sqrt(b*b+p*p),m=f[y].x+b*(c-10)/c,M=f[y].y+p*(c-10)/c,k=interpolateColors(n,e,(r[y][u]-v)/Math.max(w-v,1)),C='<marker id="arrow'+k+'" markerWidth="10" markerHeight="10" refx="6" refy="2" orient="auto" markerUnits="strokeWidth" fill="#'+k+'"><path d="M0,0 L0,4 L6,2 z"/></marker>';g+=C;var I='<line x1="'+f[y].x+'" y1 = "'+f[y].y+'" x2="'+m+'" + y2="'+M+'" stroke-width="2" stroke="#'+k+'" marker-end="url(#arrow'+k+')"/>';h+=I}for(u=0;u<t.length;u++){var B='<circle cx="'+f[u].x+'" cy="'+f[u].y+'" r="'+o+'" fill="'+nodeColors[u%nodeColors.length]+'"/>',F='<text x="'+f[u].x+'" y="'+f[u].y+'" text-anchor="middle"  fill="white" font-size="'+1.5*o+'" style="alignment-baseline:central; dominant-baseline: central;" font-family="Open Sans">'+String.fromCharCode(65+u)+"</text>";h=h+B+F}for(u=0;i>u;u++){var S='<rect x="'+(s+o+a)+'" y="'+(-s+u*a*2)+'" width="'+a+'" height="'+a+'" fill="#'+interpolateColors(n,e,1-u/i)+'"/>',P='<text x="'+(s+o+3*a)+'" y="'+(-s+u*a*2+a/2)+'"font-size="'+a+'" style="alignment-baseline:central" font-family="Open Sans">'+Math.round(w-(w-v)*u/i)+"</text>";h=h+S+P}return g+="</defs>",l+g+h+"</svg>"}function interpolateColors(t,r,n){"#"===t.charAt(0)&&(t=t.substring(1)),3===t.length&&(t=t.substring(0,1)+t.substring(0,1)+t.substring(1,2)+t.substring(1,2)+t.substring(2)+t.substring(2)),"#"===r.charAt(0)&&(t=r.substring(1)),3===r.length&&(r=r.substring(0,1)+r.substring(0,1)+r.substring(1,2)+r.substring(1,2)+r.substring(2)+r.substring(2));var e=Math.round(parseInt(t.substring(0,2),16)*(1-n)+parseInt(r.substring(0,2),16)*n),s=Math.round(parseInt(t.substring(2,4),16)*(1-n)+parseInt(r.substring(2,4),16)*n),o=Math.round(parseInt(t.substring(4),16)*(1-n)+parseInt(r.substring(4),16)*n),i=e.toString(16),a=1==i?"0"+i:i;i=s.toString(16);var l=1==i?"0"+i:i;i=o.toString(16);var h=1==i?"0"+i:i;return a+l+h}var nodeColors=["#F44336","#9C27B0","#3F51B5","#009688","#FF5722","#795548"];