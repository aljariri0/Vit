
(() => {
 document.querySelectorAll('button,a').forEach(el=>{if (/^(remove|delete|reject|hide)\b/i.test(el.textContent.trim())) el.classList.add('action-danger');});
 function paginate(host, rows, controlsHost=host) {
  if(!rows.length) return;
  const controls=document.createElement('nav'); controls.className='table-pagination';controls.setAttribute('aria-label','Table pagination');
  const prev=document.createElement('button'),next=document.createElement('button'),label=document.createElement('span');prev.textContent='Previous';next.textContent='Next';prev.type=next.type='button';prev.className=next.className='mini-action';label.setAttribute('aria-live','polite');controls.append(prev,label,next);controlsHost.after(controls);
  let page=1; const pages=Math.ceil(rows.length/8);
  const show=()=>{rows.forEach((r,i)=>{r.hidden=i<(page-1)*8||i>=page*8;if(r._detail)r._detail.hidden=r.hidden;});prev.disabled=page===1;next.disabled=page===pages;label.textContent=`Page ${page} of ${pages} · ${rows.length} records`;};
  prev.onclick=()=>{page--;show();};next.onclick=()=>{page++;show();};show();
 }
 document.querySelectorAll('.data-card').forEach(card=>{
  const rows=Array.from(card.children).filter(el=>el.classList.contains('table-row'));
  if(rows.length){const wrap=document.createElement('div');wrap.className='table-scroll';const head=card.querySelector(':scope > .table-head');card.insertBefore(wrap,head||rows[0]);if(head)wrap.append(head);rows.forEach((r,index)=>{r.classList.add(index%2===0?'stripe-light':'stripe-dark');const detail=r.nextElementSibling?.tagName==='DETAILS'?r.nextElementSibling:null;wrap.append(r);if(detail){wrap.append(detail);r._detail=detail;}});if(!card.hasAttribute('data-server-paged'))paginate(wrap,rows);}
 });
 document.querySelectorAll('table').forEach(table=>{const wrap=document.createElement('div');wrap.className='table-scroll';table.before(wrap);wrap.append(table);paginate(table,Array.from(table.tBodies).flatMap(b=>Array.from(b.rows)),wrap);});
 document.querySelectorAll('.catalog-column').forEach(card=>paginate(card,Array.from(card.querySelectorAll('.catalog-row'))));
 const dataNode=document.getElementById('dashboard-data');
 if(dataNode){if(!window.Chart){document.querySelectorAll('.chart-box').forEach(el=>el.textContent='Charts could not load. Check your internet connection.');return;}const data=JSON.parse(dataNode.textContent);new Chart(document.getElementById('sales-chart'),{type:'bar',data:{labels:data.sales.map(x=>x.day),datasets:[{label:'Sales (USD)',data:data.sales.map(x=>Number(x.revenue)),backgroundColor:'#486d45',borderRadius:6}]},options:{responsive:true,maintainAspectRatio:false,scales:{y:{beginAtZero:true}},plugins:{legend:{display:false}}}});new Chart(document.getElementById('status-chart'),{type:'doughnut',data:{labels:data.statuses.map(x=>x.status),datasets:[{data:data.statuses.map(x=>Number(x.count)),backgroundColor:['#244c38','#8eac72','#d8b578','#a3bbc0','#e58a71','#8b809b','#c4cbad']}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}}}});}
 if(dataNode && window.Chart){
  const data=JSON.parse(dataNode.textContent);
  const charts=[['top-products-chart','bar',data.topProducts,'name','units','Units sold'],['category-sales-chart','bar',data.categorySales,'name','revenue','Product sales (USD)'],['customer-growth-chart','line',data.customerGrowth,'month','customers','New customers']];
  charts.forEach(([id,type,rows,label,value,title])=>{
   const canvas=document.getElementById(id);
   if(!rows.length){canvas.parentElement.textContent='No data yet. This chart will appear as activity grows.';return;}
   new Chart(canvas,{type,data:{labels:rows.map(r=>r[label]),datasets:[{label:title,data:rows.map(r=>Number(r[value])),backgroundColor:'#78975d',borderColor:'#244c38',borderWidth:2,borderRadius:5,tension:.25}]},options:{responsive:true,maintainAspectRatio:false,scales:{y:{beginAtZero:true,ticks:{precision: value==='revenue'?2:0}}},plugins:{legend:{display:false}}}});
  });
 }

})();

