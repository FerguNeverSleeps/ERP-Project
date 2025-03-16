/**
 * This entity represents an organization which is a container for projects, users and
 * groups.
 */
Ext.define('Personal.model.Puestos', {
    extend: 'Ext.data.Model',

    fields: [
        { name: 'id_puesto',   type: 'int' },
        { name: 'id_cliente',  type: 'int'},
        { name: 'cliente',     type: 'string'},
        { name: 'codigo',      type: 'string'},
        { name: 'descripcion', type: 'string'},
        { name: 'ubicacion',   type: 'string'},
        { name: 'tipo_turno',  type: 'int'},
        { name: 'telefono',    type: 'string'},
        { name: 'dia1_desde',  type: 'string'},
        { name: 'dia1_hasta',  type: 'string'},
        { name: 'dia2_desde',  type: 'string'},
        { name: 'dia2_hasta',  type: 'string'},
        { name: 'dia3_desde',  type: 'string'},
        { name: 'dia3_hasta',  type: 'string'},
        { name: 'dia4_desde',  type: 'string'},
        { name: 'dia4_hasta',  type: 'string'},
        { name: 'dia5_desde',  type: 'string'},
        { name: 'dia5_hasta',  type: 'string'},
        { name: 'dia6_desde',  type: 'string'},
        { name: 'dia6_hasta',  type: 'string'},
        { name: 'dia7_desde',  type: 'string'},         
        { name: 'dia7_hasta',  type: 'string'}
    ]
    
});
