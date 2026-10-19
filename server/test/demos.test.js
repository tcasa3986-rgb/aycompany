const { test, before, after } = require('node:test');
const assert = require('node:assert/strict');
const express = require('express');
const jwt = require('jsonwebtoken');
const paquete = require('../src/services/demos/paqueteDemo');
const visitas = require('../src/services/demos/visitasDemo');
const { persistirPaquete } = require('../src/services/demos/persistirPaquete');
const negocio = { id: 1, nombre: 'Clínica de prueba', ciudad: 'Ibagué', rubro: 'odontología',
    direccion: 'Dirección de prueba', telefono: '+57 300 000 0000', demo_slug: 'clinica-prueba-123456' };

test('normaliza WhatsApp sin duplicar 57 y no inventa móviles para fijos', () => {
    assert.equal(paquete.telefonoCO('+57 300 000 0000'),'573000000000');
    assert.equal(paquete.telefonoCO('300 000 0000'),'573000000000');
    assert.equal(paquete.telefonoCO('608 123 4567'),null);
    assert.equal(paquete.telefonoCO('123'),null);
});
test('propuesta sin compromiso no exige ni inventa un precio', () => {
    const p=paquete.validarPropuesta({revisado:true,aprobada:true});
    assert.equal(p.inicial,null);assert.equal(p.mensual,null);
    const html=paquete.construirPropuesta(negocio,p);
    assert.match(html,/sin ningún compromiso/);assert.match(html,/opción económica/);
    assert.match(html,/estructura técnica pensada para posicionamiento/);
    assert.doesNotMatch(html,/<div class="prices">/);
    assert.doesNotMatch(html,/\$ 0/);
});
test('rechaza aprobaciones sin revisión, importes y alcances inválidos', () => {
    assert.throws(()=>paquete.validarPropuesta({aprobada:true}),/Confirma/);
    for(const inicial of [-1,NaN,Infinity,'1000',0.5]) assert.throws(()=>paquete.validarPropuesta({inicial}));
    assert.throws(()=>paquete.validarPropuesta({alcance:[]}));
    assert.throws(()=>paquete.validarPropuesta({alcance:['<dato>',{}]}));
});
test('metadatos sobreviven al render y no permiten cerrar su script', () => {
    const p=paquete.validarPropuesta({condiciones:'</script><img src=x onerror=alert(1)>'});
    const html=paquete.construirDemo({...negocio,nombre:'<img src=x onerror=alert(1)>'},p);
    assert.doesNotMatch(html,/<img src=x/);
    assert.equal(paquete.leerPaquete(html).propuesta.condiciones,p.condiciones);
    assert.equal(paquete.leerPaquete('<html>demo anterior</html>'),null);
});
test('demo tiene imágenes, propuesta comercial propia y ningún marcador sin resolver', () => {
    const html=paquete.construirDemo(negocio);
    assert.match(html,/\/demo-assets\/consultorio-ilustrativo.webp/);
    assert.match(html,/Referencia con IA/);assert.match(html,/\/propuesta\/clinica-prueba-123456/);
    assert.doesNotMatch(html,/\{\{[A-Z_]+\}\}/);
    assert.doesNotMatch(html,/años de experiencia|Te respondemos hoy|pacientes nos calificaron/);
});
test('otro rubro no recibe fotos odontológicas ni tratamientos inventados', () => {
    const html=paquete.construirDemo({...negocio,rubro:'taller mecánico',nombre:'Taller de prueba'});
    assert.doesNotMatch(html,/src="\/demo-assets\//);
    assert.match(html,/fotografías autorizadas/);
});
test('plantillas mantienen distintos enfoques a partir del nombre del negocio', () => {
    assert.match(paquete.contexto({...negocio,nombre:'ABC Ortodoncia'}).titular,/próxima sonrisa/);
    assert.match(paquete.contexto({...negocio,nombre:'Odontología Estética'}).titular,/nueva mirada/);
    assert.equal(paquete.contexto({...negocio,nombre:'DRA. CAMILA ORJUELA, ODONTOLOGIA ESTÉTICA, CIRUGÍA MAXILOFACIAL'}).marca,'Dra. Camila Orjuela');
    assert.equal(paquete.contexto({...negocio,nombre:'Dentoman Clínica Odontológica'}).marca,'Dentoman');
});
test('firma de visitas expira, está ligada al slug y rechaza firmas alteradas', () => {
    const now=1792330000000,token=visitas.tokenVisita('abc','pruebas',now);
    assert.equal(visitas.tokenValido(token,'abc','pruebas',now),true);
    assert.equal(visitas.tokenValido(token,'otro','pruebas',now),false);
    assert.equal(visitas.tokenValido(token,'abc','pruebas',now+visitas.TTL),false);
    assert.equal(visitas.tokenValido('a','abc','pruebas',now),false);
    assert.equal(visitas.tokenVisita('abc',''),null);
});
test('la medición espera interacción y la cookie permite deduplicar', () => {
    const token=visitas.tokenVisita('abc','pruebas');
    assert.equal(visitas.yaContada({headers:{cookie:visitas.nombreCookie('abc')+'='+token}},'abc','pruebas'),true);
    const html=visitas.conMedicion('<body>demo</body>','abc',token);
    assert.match(html,/pointerdown/);assert.match(html,/visibilityState/);assert.doesNotMatch(html,/DOMContentLoaded/);
});

function fila(html='<html>anterior</html>') {
    return {...negocio, demo_html:html,estado:'encontrado',visitas:2,
        get(){return {...this};},async update(d){Object.assign(this,d);},async increment(){this.visitas++;}};
}
test('preparar respalda antes de reemplazar, conserva slug y no cambia estado comercial',async()=>{
    const n=fila();n.estado='respondio';const eventos=[];
    const sequelize={transaction:fn=>fn({LOCK:{UPDATE:'UPDATE'}})};
    const modelos={NegocioSinWeb:{findByPk:async(id,options)=>{assert.equal(options.lock,'UPDATE');return n;}},
        DemoVersion:{create:async d=>eventos.push(d.html)}};
    await persistirPaquete({...modelos,sequelize,id:1,crearSlug:()=>{throw Error('no debe cambiar slug');}});
    assert.deepEqual(eventos,['<html>anterior</html>']);assert.equal(n.estado,'respondio');
    assert.equal(n.demo_slug,negocio.demo_slug);assert.ok(paquete.leerPaquete(n.demo_html));
    await persistirPaquete({...modelos,sequelize,id:1,crearSlug:()=>''});assert.equal(eventos.length,1);
});
test('si falla el respaldo no actualiza ni pierde la demo anterior',async()=>{
    const n=fila();await assert.rejects(()=>persistirPaquete({id:1,crearSlug:()=>'',
        sequelize:{transaction:fn=>fn({LOCK:{UPDATE:'UPDATE'}})},
        NegocioSinWeb:{findByPk:async()=>n},DemoVersion:{create:async()=>{throw Error('sin respaldo');}}}),/sin respaldo/);
    assert.equal(n.demo_html,'<html>anterior</html>');
});

// HTTP local: controlador y middleware reales, repositorio en memoria. No se
// importa app.js, no hay conexión a MySQL, trabajadores ni envíos externos.
let server,base,n,ctrl;
const secretAnterior=process.env.JWT_SECRET;
before(async()=>{
    process.env.JWT_SECRET='secreto-exclusivo-de-pruebas';n=fila();
    const modelos={NegocioSinWeb:{findOne:async options=>options.where.demo_slug===n.demo_slug?n:null,findByPk:async()=>n,
        findAll:async()=>[n]},DemoVersion:{create:async()=>{}}};
    const db={transaction:fn=>fn({LOCK:{UPDATE:'UPDATE'}})};
    for(const [file,exports] of [['../src/models',modelos],['../src/config/db',db]]){
        const id=require.resolve(file);require.cache[id]={id,filename:id,loaded:true,exports};
    }
    ctrl=require('../src/controllers/cazadorController');
    const app=express();app.use(express.json());
    app.get('/demo/:slug',ctrl.verDemo);app.get('/propuesta/:slug',ctrl.verPropuesta);app.post('/demo/:slug/visita',ctrl.registrarVisita);
    app.use('/api/cazador',require('../src/routes/cazadorRoutes'));
    server=await new Promise(resolve=>{const s=app.listen(0,'127.0.0.1',()=>resolve(s));});
    base=`http://127.0.0.1:${server.address().port}`;
});
after(()=>{server?.close();if(secretAnterior===undefined)delete process.env.JWT_SECRET;else process.env.JWT_SECRET=secretAnterior;});
test('GET y HEAD públicos no suman ni cambian estado',async()=>{
    const antes=n.visitas,estado=n.estado;
    for(const method of ['GET','HEAD']){const r=await fetch(base+'/demo/'+n.demo_slug,{method});assert.equal(r.status,200);assert.match(r.headers.get('x-robots-tag'),/noindex/);}
    assert.equal(n.visitas,antes);assert.equal(n.estado,estado);
});
test('preview requiere administrador y la vista válida no suma visitas',async()=>{
    let r=await fetch(base+'/api/cazador/1/preview');assert.equal(r.status,401);
    const token=rol=>jwt.sign({id:1,rol},process.env.JWT_SECRET);
    r=await fetch(base+'/api/cazador/1/preview',{headers:{Authorization:'Bearer '+token('vendedor')}});assert.equal(r.status,403);
    const antes=n.visitas;r=await fetch(base+'/api/cazador/1/preview',{headers:{Authorization:'Bearer '+token('admin')}});
    assert.equal(r.status,200);const body=await r.json();assert.match(body.html,/demo-paquete/);assert.doesNotMatch(body.html,/keepalive/);assert.equal(n.visitas,antes);
});
test('propuesta pública tiene el nombre correcto y contacto de AI Company',async()=>{
    const r=await fetch(base+'/propuesta/'+n.demo_slug);assert.equal(r.status,200);
    const html=await r.text();assert.match(html,/Clínica de prueba/);assert.match(html,/Conversar con Cristian/);
});
test('visita rechaza firma/origen inválido y acepta interacción una vez con cookie',async()=>{
    const url=base+'/demo/'+n.demo_slug+'/visita';const token=visitas.tokenVisita(n.demo_slug,process.env.JWT_SECRET);
    const post=(origin,t,cookie)=>fetch(url,{method:'POST',headers:{'Content-Type':'application/json',Origin:origin,...(cookie?{Cookie:cookie}:{})},body:JSON.stringify({token:t})});
    assert.equal((await post(base,'incorrecto')).status,403);assert.equal((await post('https://otro.example',token)).status,403);
    const antes=n.visitas;n.estado='enviado';let r=await post(base,token);assert.equal(r.status,200);assert.equal(n.visitas,antes+1);assert.equal(n.estado,'abrio');
    const cookie=r.headers.get('set-cookie').split(';')[0];r=await post(base,token,cookie);assert.equal((await r.json()).contada,false);assert.equal(n.visitas,antes+1);
});
test('abrir un borrador no se considera envío y el servidor impide enviar sin revisión',async()=>{
    n.demo_html='<html>legacy</html>';const token=jwt.sign({id:1,rol:'admin'},process.env.JWT_SECRET);
    const r=await fetch(base+'/api/cazador/1',{method:'PUT',headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},body:JSON.stringify({estado:'enviado'})});
    assert.equal(r.status,400);
});
test('listado no expone todo el HTML y muestra los metadatos del paquete',async()=>{
    const token=jwt.sign({id:1,rol:'admin'},process.env.JWT_SECRET);n.demo_html=paquete.construirDemo(n);
    const r=await fetch(base+'/api/cazador',{headers:{Authorization:'Bearer '+token}});assert.equal(r.status,200);
    const body=await r.json();assert.equal('demo_html' in body.data[0],false);assert.equal(body.data[0].paquete.version,paquete.VERSION);
});
