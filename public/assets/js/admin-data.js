/* Fictional fixtures: field names mirror the supplied vitemines SQL. */
const demoDate = '2026-10-29';
const seed = {
 categories: ['Vitamins','Minerals','Omega & oils','Daily wellness'].map((name,i)=>({id:i+1,name,description:['Everyday vitamin essentials','Essential mineral support','Omega supplements and oils','A balanced daily routine'][i],image:'',is_active:1,created_by_admin_id:1,created_at:demoDate})),
 brands: ['Nature Made','NOW Foods','Solgar','Garden of Life','Life Extension'].map((name,i)=>({id:i+1,name,logo:'',discount_percent:i===1?10:0,created_by_admin_id:1,created_at:demoDate})),
 users: ['Nour Salem','Ahmad Ali','Sara Omar','Omar Khaled','Lina Hassan'].map((name,i)=>({id:i+1,name,email:name.toLowerCase().replace(' ','.')+'@example.com',phone:'',address:'Demo address',city:['Amman','Irbid','Amman','Zarqa','Aqaba'][i],is_blocked_from_commenting:i===3?1:0,blocked_by_admin_id:i===3?1:null,created_at:demoDate,updated_at:demoDate})),
 products: ['Vitamin D3 1000 IU','Vitamin C 500 mg','Magnesium 200 mg','Omega-3 Fish Oil','Daily Multivitamin','Zinc 30 mg','Vitamin B12','Calcium + D3'].map((name,i)=>({id:i+1,name,category_id:[1,1,2,3,4,2,1,2][i],brand_id:i%5+1,description:'Demo product for the storefront catalog.',ingredients:'See product packaging.',warnings:'Read the product label before use.',price:[12.5,9.75,18,24.5,21,11,16.5,19][i],discount_percent:i===1?10:0,stock:[84,126,8,42,0,65,31,18][i],sizes:['60 capsules','120 capsules'],weight:0.15,image:[],is_active:i===4?0:1,created_by_admin_id:1,updated_by_admin_id:1,created_at:demoDate,updated_at:demoDate})),
 reviews: ['Arrived well packed. Very happy with my order.','Clear packaging and quick delivery.','Good shopping experience.','The delivery took longer than expected.','Easy to order and nicely packaged.'].map((comment,i)=>({id:i+1,product_id:i+1,user_id:i+1,star_rating:[5,4,5,3,4][i],comment,image:'',moderated_by_admin_id:null,created_at:demoDate,updated_at:demoDate,rating_avg:0,rating_count:0}))
};
let db;
try { db=JSON.parse(localStorage.getItem('vita-admin-original-v1')) || structuredClone(seed); if(!Object.keys(seed).every(k=>Array.isArray(db[k]))) throw Error(); } catch { db=structuredClone(seed); }
function persist(){try{localStorage.setItem('vita-admin-original-v1',JSON.stringify(db));}catch{ /* Session-only demo when storage is unavailable. */ }}
