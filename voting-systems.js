function generateRandomVotes(r,e){for(var n=[],t=0;t<r.length;t++)n.push(t+1);var o=[];for(t=0;e>t;t++){var a={};n=shuffle(n);for(var s=0;s<r.length;s++)a[r[s]]=n[s];o.push(a)}return o}function shuffle(r){for(var e=[],n=[],t=0;t<r.length;t++)e.push(t);for(t=0;t<r.length;t++){var o=e.splice(Math.floor(Math.random()*e.length),1);n.push(r[o])}return n}function getDistances(r,e){for(var n=[],t=0;t<r.length;t++){for(var o=[],a=0;a<r.length;a++){for(var s=0,f=0;f<e.length;f++)e[f][r[t]]<e[f][r[a]]&&s++;o.push(s)}n.push(o)}return n}function schulze(r,e){for(var n,t=getDistances(r,e),o=[],a=0;a<r.length;a++){var s=[];for(n=0;n<r.length;n++)s.push(t[a][n]>t[n][a]?t[a][n]:0);o.push(s)}for(a=0;a<r.length;a++)for(n=0;n<r.length;n++)if(a!==n)for(var f=0;f<r.length;f++)f!==a&&f!==n&&(o[n][f]=Math.max(o[n][f],Math.min(o[n][a],o[a][f])));var l=function(r,e){return o[r][e]>o[e][r]?-1:o[e][r]>o[r][e]?1:0},h=[];for(a=0;a<r.length;a++)h.push(a);h.sort(l);var c=h.map(function(e){return{film:r[e],index:e}});for(c[0].rank=1,a=1;a<r.length;a++)c[a].rank=o[c[a].index][c[a-1].index]===o[c[a-1].index][c[a].index]?c[a-1].rank:a+1;return c}function copeland(r,e){for(var n=getDistances(r,e),t=[],o=0;o<r.length;o++){for(var a=0,s=0;s<r.length;s++)o!==s&&(n[o][s]>n[s][o]?a++:n[o][s]<n[s][o]&&a--);t.push({film:r[o],score:a,rank:o})}for(t.sort(function(r,e){return e.score-r.score}),t[0].rank=1,o=1;o<r.length;o++)t[o].rank=t[o].score===t[o-1].score?t[o-1].rank:o+1;return t}function minimax(r,e){for(var n=getDistances(r,e),t=[],o=0;o<r.length;o++){for(var a=0,s=0;s<r.length;s++)n[s][o]>n[o][s]&&n[s][o]>a&&(a=n[s][o]);t.push({film:r[o],score:a,rank:o})}for(t.sort(function(r,e){return r.score-e.score}),t[0].rank=1,o=1;o<r.length;o++)t[o].rank=t[o].score===t[o-1].score?t[o-1].rank:o+1;return t}function borda(r,e,n){null==n&&(n=r.length+1);for(var t=[],o=0;o<r.length;o++)t.push({film:r[o],score:0,rank:o});for(var a=0;a<r.length;a++)for(var s=0;s<e.length;s++)t[a].score=t[a].score-e[s][t[a].film]+n;for(t.sort(function(r,e){return e.score-r.score}),t[0].rank=1,a=1;a<r.length;a++)t[a].rank=t[a].score===t[a-1].score?t[a-1].rank:a+1;return t}function kemenyYoung(r,e){for(var n=getDistances(r,e),t=-1,o=!0,a=[],s=[],f=0;f<r.length;f++)s.push(f);var l=permutator(s);for(f=0;f<l.length;f++){for(var h=0,c=0;c<r.length-1;c++)for(var i=c+1;i<r.length;i++)h+=n[l[f][c]][l[f][i]];h===t&&(o=!1),h>t&&(t=h,a=l[f],o=!0)}if(!o)throw"Draw for the higest score";var u=a.map(function(e,n){return{film:r[e],rank:n+1}});return u}function permutator(r){function e(r,t){for(var o=0;o<r.length;o++){var a=r.splice(o,1);0===r.length&&n.push(t.concat(a)),e(r.slice(),t.concat(a)),r.splice(o,0,a[0])}return n}var n=[];return e(r,[])}function baldwin(r,e){for(var n=r.slice(),t=JSON.parse(JSON.stringify(e)),o=[];n.length>0;)for(var a=borda(n,t,r.length+1),s=a[a.length-1].score,f=a.length-1;f>-1&&a[f].score===s;f--)removeCandidate(n,t,a[f].film),o.push(a[f]);return o.reverse(),o}function removeCandidate(r,e,n){r.splice(r.indexOf(n),1);for(var t=0;t<e.length;t++)for(var o=e[t][n],a=0;a<r.length;a++)e[t][r[a]]>o&&e[t][r[a]]--}function nanson(r,e){for(var n=r.slice(),t=JSON.parse(JSON.stringify(e)),o=[];n.length>0;){for(var a=borda(n,t,r.length+1),s=0,f=0;f<a.length;f++)s+=a[f].score/a.length;for(f=a.length-1;f>-1&&a[f].score<=s;f--)removeCandidate(n,t,a[f].film),o.push(a[f])}return o.reverse()}function av(r,e){for(var n=r.slice(),t=JSON.parse(JSON.stringify(e)),o=[];n.length>0;){var a=plurality(n,t);if(a[0].score>e.length/2)return o=o.concat(a.reverse()),o.reverse();for(var s=a[a.length-1].score,f=a.length-1;f>0&&a[f].score===s;f--)removeCandidate(n,t,a[f].film),o.push(a[f])}return o.reverse()}function plurality(r,e){for(var n=[],t=0;t<r.length;t++){for(var o={film:r[t],score:0,rank:t},a=0;a<e.length;a++)1===e[a][r[t]]&&o.score++;n.push(o)}for(n.sort(function(r,e){return e.score-r.score}),n[0].rank=1,t=1;t<r.length;t++)n[t].rank=n[t].score===n[t-1].score?n[t-1].rank:t+1;return n}