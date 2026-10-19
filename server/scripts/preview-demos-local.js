// Revisión local sin .env, base de datos, trabajadores ni canales de mensajes.
// Los nombres se tomaron de la lista pública de demos revisada. Los demás
// campos son mínimos de muestra: no representa una exportación de producción.
if (process.env.NODE_ENV === 'production') throw new Error('Esta herramienta sólo funciona en desarrollo');
const express = require('express');
const path = require('path');
const nombres = [
    'Dentoman Clínica Odontológica', 'ALEJANDRA SANCHEZ ODONTOLOGIA',
    'DENTALWORK, DRA. JENNIFFER ESPINOSA',
    'DRA. CAMILA ORJUELA, ODONTOLOGIA ESTÉTICA, CIRUGÍA MAXILOFACIAL, ORTODONCIA',
    'Odontología Ibagué New denti', 'Odontologia Estetica Dr. Jerson Cervera',
    'IMPLANTA ODONTOLOGÍA -DR. MICHAEL PINEDA', 'ABC Ortodoncia clínica Odontologica',
    'Clinica Odontologica Especilizada la Vallot', 'E.O. Emergencias Odontologicas',
];
const filas=nombres.map((nombre,i)=>({id:i+1,nombre,ciudad:'Ibagué',rubro:'odontología',score:100-i*4,
    direccion:i===0?'Edificio de Profesionales Coomeva, Carrera 5 #38-14, consultorios 203–204':null,
    telefono:null,demo_slug:'revision-'+(i+1),demo_html:i<7?'<html>Versión anterior de muestra</html>':null,
    estado:i<7?'demo_generada':'encontrado',visitas:0,
    get(){return {...this};},async update(datos){Object.assign(this,datos);},async increment(){this.visitas++;}}));
const backups=[];
const modelos={NegocioSinWeb:{findByPk:async id=>filas.find(n=>n.id===Number(id)),
    findOne:async options=>filas.find(n=>n.demo_slug===options.where.demo_slug),
    findAll:async options=>options?.attributes?filas:options?.where?.visitas?[]:filas.filter(n=>!options?.where?.estado||n.estado===options.where.estado)},
    DemoVersion:{create:async data=>backups.push(data)}};
for(const [file,exports] of [['../src/models',modelos],['../src/config/db',{transaction:fn=>fn({LOCK:{UPDATE:'UPDATE'}})}]]){
    const id=require.resolve(file);require.cache[id]={id,filename:id,loaded:true,exports};
}
const ctrl=require('../src/controllers/cazadorController');
const app=express();app.use(express.json());
app.use((req,res,next)=>{res.set('Access-Control-Allow-Origin','http://127.0.0.1:5198');res.set('Access-Control-Allow-Headers','Content-Type,Authorization');res.set('Access-Control-Allow-Methods','GET,POST,PUT,OPTIONS');if(req.method==='OPTIONS')return res.sendStatus(204);next();});
app.get('/api/cazador',ctrl.listar);app.get('/api/cazador/:id/preview',ctrl.preview);
app.post('/api/cazador/:id/generar',ctrl.generarDemo);app.put('/api/cazador/:id',ctrl.actualizar);
app.post('/api/cazador/buscar',(req,res)=>res.status(409).json({ok:false,msg:'La búsqueda de Google está deshabilitada en esta prueba local.'}));
app.get('/demo/:slug',ctrl.verDemo);app.get('/propuesta/:slug',ctrl.verPropuesta);
app.use('/demo-assets',express.static(path.join(__dirname,'../public/demo-assets')));
app.get('/estado-local',(req,res)=>res.json({local:true,backups:backups.length,negocios:filas.length}));
app.listen(5197,'127.0.0.1',()=>console.log('Demos locales: http://127.0.0.1:5197/demo/revision-1 · Sin producción'));
