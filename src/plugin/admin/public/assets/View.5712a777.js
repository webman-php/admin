var T=(n,h,r)=>new Promise((o,s)=>{var m=t=>{try{i(r.next(t))}catch(p){s(p)}},a=t=>{try{i(r.throw(t))}catch(p){s(p)}},i=t=>t.done?o(t.value):Promise.resolve(t.value).then(m,a);i((r=r.apply(n,h)).next())});import{B as N,u as O}from"./useTable.1f9e9a8e.js";import{x as D}from"./BasicForm.82e222ab.js";import{aJ as H,r as M,a as L,d1 as V,dO as j,cF as q,ac as G,aL as _,o as J,h as K,j as v,p as w,q as z,cy as P,x as Q}from"./index.f8bcf808.js";import{T as u}from"./table.0ba2e59f.js";import{b as W}from"./index.a4e346ff.js";import X from"./Update.339ad87a.js";import{a as Y,b as Z,g as ee}from"./common.4f9a0f43.js";import{t as te}from"./util.85659bac.js";import"./index.d82b2be8.js";import"./useForm.15b4451f.js";import"./index.d4c4849e.js";import"./index.40c44490.js";import"./index.060f9061.js";import"./index.0e7d1863.js";import"./index.8cf0b441.js";import"./useWindowSizeFn.d2a0a89b.js";import"./useContentViewHeight.0d100a60.js";import"./ArrowLeftOutlined.73a6b26e.js";import"./transButton.ce0379fb.js";import"./index.05080ca7.js";import"./index.b5bef3a7.js";import"./_baseIteratee.f49fbaac.js";import"./index.91cad2d2.js";import"./sortable.esm.c20789c1.js";import"./RedoOutlined.ed3d1686.js";import"./FullscreenOutlined.f88b6f65.js";import"./fromPairs.2341303e.js";import"./scrollTo.d51d60f9.js";import"./index.fe83b607.js";import"./index.bf9f5082.js";import"./index.14ba2351.js";import"./index.293b5840.js";import"./index.26a767f7.js";import"./uniqBy.0ae55b98.js";import"./download.b66616ed.js";import"./index.f2e779e6.js";let f="",y="",g="",b="",C="";const c=M({schemas:[]}),oe=L({components:{ModalInserOrEdit:X,BasicTable:N,TableAction:D},setup(){var R,k;const{createMessage:n}=Q(),{success:h}=n,r=V(),o=(k=(R=r.params)==null?void 0:R.id)!=null?k:"",s=r.path;if(o){f=u.SELECT+"?table="+o,y=u.INSERT+"?table="+o,g=u.UPDATE+"?table="+o,C=u.DELETE+"?table="+o,b=u.SCHEMA+"?table="+o;const{setTitle:l}=j();l(`${o}\u8868`)}else f=`/app/admin${s}/select`,y=`/app/admin${s}/insert`,g=`/app/admin${s}/update`,C=`/app/admin${s}/delete`,b=`/app/admin${s}/schema`;const m=M([]),a=M("");q(()=>T(this,null,function*(){const l=yield Y(b),U=l.columns;for(let e of U)if(e.primary_key){a.value=e.field;break}const x=l.forms;c.value.schemas=[];for(let e of x){if(e.searchable){let[E,$]=te(e,"","search");e.search_type=="between"?(c.value.schemas.push({field:`${e.field}[0]`,component:E,label:e.comment||e.field,colProps:{offset:1,span:5},componentProps:$}),c.value.schemas.push({field:`${e.field}[1]`,component:E,label:"\u3000\u5230",colProps:{span:5},componentProps:$})):c.value.schemas.push({field:e.field,component:E,label:e.comment||e.field,colProps:{offset:1,span:10},componentProps:$})}let A={dataIndex:e.field,title:e.comment||e.field,sorter:e.enable_sort,defaultHidden:!e.list_show};["InputNumber","Switch"].indexOf(e.control)!=-1&&(A.width=120),m.value.push(A)}c.value.schemas.length||G(()=>{I({useSearchForm:!1})})}));const[i,{openModal:t}]=W(),[p,{reload:d,setProps:I}]=O({title:`${o}table data`,api:ee(f),columns:m,useSearchForm:!0,bordered:!0,formConfig:c,actionColumn:{width:250,title:"Action",dataIndex:"action",slots:{customRender:"action"}}});function S(l){return T(this,null,function*(){if(!a.value){P("The current table has no primary key and cannot be edited");return}t(!0,{selectUrl:f,insertUrl:y,updateUrl:g,schemaUrl:b,column:a.value,value:l[a.value]})})}function B(l){return T(this,null,function*(){if(!a.value){P("The current table has no primary key and cannot be deleted");return}yield Z(C,{column:a.value,value:l[a.value]}),h("successfully deleted"),d()})}function F(){t(!0,{selectUrl:f,insertUrl:y,updateUrl:g,schemaUrl:b})}return{registerTable:p,handleEdit:S,handleDelete:B,openRowModal:F,register:i,reload:d}}}),ae={class:"p-4"};function ne(n,h,r,o,s,m){const a=_("a-button"),i=_("TableAction"),t=_("BasicTable"),p=_("ModalInserOrEdit");return J(),K("div",ae,[v(t,{onRegister:n.registerTable,showTableSetting:""},{toolbar:w(()=>[v(a,{type:"primary",onClick:n.openRowModal},{default:w(()=>[z(" Add record ")]),_:1},8,["onClick"])]),action:w(({record:d})=>[v(i,{actions:[{label:"edit",onClick:n.handleEdit.bind(null,d)},{label:"delete",icon:"ic:outline-delete-outline",popConfirm:{title:"delete or not\uFF1F",confirm:n.handleDelete.bind(null,d)}}]},null,8,["actions"])]),_:1},8,["onRegister"]),v(p,{onRegister:n.register,minHeight:300,onReload:n.reload},null,8,["onRegister","onReload"])])}var Ve=H(oe,[["render",ne]]);export{Ve as default};
