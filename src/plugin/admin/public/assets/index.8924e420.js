var b=(t,f,n)=>new Promise((a,m)=>{var p=o=>{try{r(n.next(o))}catch(i){m(i)}},d=o=>{try{r(n.throw(o))}catch(i){m(i)}},r=o=>o.done?a(o.value):Promise.resolve(o.value).then(p,d);r((n=n.apply(t,f)).next())});import{B as I,u as x}from"./useTable.6f993526.js";import{x as A}from"./BasicForm.1fff6f7d.js";import{a as E,b as F,g as $}from"./common.b6407f2a.js";import{b as S}from"./index.aafb2f3b.js";import U from"./Update.2e798ee3.js";import{aJ as O,r as w,a as N,cF as D,ac as H,aL as _,o as K,h as V,j as v,p as g,q as j,dK as q,cy as k,x as G}from"./index.02bbedaf.js";import{t as J}from"./util.3d1d31fd.js";import"./index.2a917be4.js";import"./useForm.63af36a7.js";import"./index.c7c98e3b.js";import"./index.02061880.js";import"./index.f5f0f8b0.js";import"./index.9a84248c.js";import"./index.5be6cedd.js";import"./useWindowSizeFn.c5b50549.js";import"./useContentViewHeight.7a5f4790.js";import"./ArrowLeftOutlined.e922311e.js";import"./transButton.1ecc7651.js";import"./index.29c90a5d.js";import"./index.d5cdfa6a.js";import"./_baseIteratee.2b89f8d1.js";import"./index.441385e5.js";import"./sortable.esm.2632adaa.js";import"./RedoOutlined.f840e480.js";import"./FullscreenOutlined.a5f55a02.js";import"./fromPairs.84aabb58.js";import"./scrollTo.dc5d511b.js";import"./index.99634e7a.js";import"./index.37505707.js";import"./index.47561936.js";import"./index.125d35f2.js";import"./index.00a4dc86.js";import"./uniqBy.e456814b.js";import"./download.d180e438.js";import"./index.4792e25b.js";const y="/app/admin/auth/admin/select",C="/app/admin/auth/admin/insert",M="/app/admin/auth/admin/update",L="/app/admin/auth/admin/delete",T="/app/admin/auth/admin/schema",c=w({schemas:[]}),z=N({components:{ModalInserOrEdit:U,BasicTable:I,TableAction:A},setup(){const{createMessage:t}=G(),{success:f}=t,n=w([]),a=w("");D(()=>b(this,null,function*(){const l=yield E(T),P=l.columns;for(let e of P)if(e.primary_key){a.value=e.field;break}const B=l.forms;c.value.schemas=[];for(let e of B){if(e.searchable){let[s,u]=J(e,"","search");e.search_type=="between"?(c.value.schemas.push({field:`${e.field}[0]`,component:s,label:e.comment||e.field,colProps:{offset:1,span:5},componentProps:u}),c.value.schemas.push({field:`${e.field}[1]`,component:s,label:"\u3000\u5230",colProps:{span:5},componentProps:u})):c.value.schemas.push({field:e.field,component:s,label:e.comment||e.field,colProps:{offset:1,span:10},componentProps:u})}if(e.list_show){let s={dataIndex:e.field,title:e.comment||e.field,sorter:e.enable_sort,defaultHidden:!e.list_show};["InputNumber","Switch"].indexOf(e.control)!=-1&&(s.width=120),e.field=="avatar"&&(s.width=50,s.customRender=({record:u})=>q("img",{src:u[e.field]})),n.value.push(s)}}c.value.schemas.length||H(()=>{o({useSearchForm:!1})})}));const[m,{openModal:p}]=S(),[d,{reload:r,setProps:o}]=x({api:$(y),columns:n,useSearchForm:!0,bordered:!0,formConfig:c,actionColumn:{width:250,title:"Action",dataIndex:"action",slots:{customRender:"action"}}});function i(l){return b(this,null,function*(){if(!a.value){k("The current table has no primary key and cannot be edited");return}p(!0,{selectUrl:y,insertUrl:C,updateUrl:M,schemaUrl:T,column:a.value,value:l[a.value]})})}function h(l){return b(this,null,function*(){if(!a.value){k("The current table has no primary key and cannot be deleted");return}yield F(L,{column:a.value,value:l[a.value]}),f("successfully deleted"),r()})}function R(){p(!0,{selectUrl:y,insertUrl:C,updateUrl:M,schemaUrl:T})}return{registerTable:d,handleEdit:i,handleDelete:h,openRowModal:R,register:m,reload:r}}}),Q={class:"p-4"};function W(t,f,n,a,m,p){const d=_("a-button"),r=_("TableAction"),o=_("BasicTable"),i=_("ModalInserOrEdit");return K(),V("div",Q,[v(o,{onRegister:t.registerTable,showTableSetting:""},{toolbar:g(()=>[v(d,{type:"primary",onClick:t.openRowModal},{default:g(()=>[j(" Add record ")]),_:1},8,["onClick"])]),action:g(({record:h})=>[v(r,{actions:[{label:"edit",onClick:t.handleEdit.bind(null,h)},{label:"delete",icon:"ic:outline-delete-outline",popConfirm:{title:"delete or not\uFF1F",confirm:t.handleDelete.bind(null,h)}}]},null,8,["actions"])]),_:1},8,["onRegister"]),v(i,{onRegister:t.register,minHeight:300,onReload:t.reload},null,8,["onRegister","onReload"])])}var Fe=O(z,[["render",W]]);export{Fe as default};
