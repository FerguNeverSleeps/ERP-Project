/**
 * This entity represents an organization which is a container for projects, users and
 * groups.
 */
Ext.define('Personal.model.Posicion', {
    extend: 'Ext.data.Model',

    fields: [
        { name: 'nomposicion_id', type: 'int' },
        { name: 'descripcion_posicion', type: 'string'},
        { name: 'sueldo_propuesto', type: 'float' },
        { name: 'sueldo_anual', type: 'float' },
    	{ name: 'partida', type: 'string'},
    	{ name: 'gastos_representacion', type: 'float' },
    	{ name: 'paga_gr', type: 'int' },
        { name: 'categoria_id', type: 'int' },
        { name: 'cargo_id', type: 'int' },
        { name: 'cod_nivel1', type: 'int' },
        { name: 'cod_nivel2', type: 'int' },
        { name: 'cod_nivel3', type: 'int' },
        { name: 'cod_nivel4', type: 'int' },
        { name: 'cod_nivel5', type: 'int' },
        { name: 'cod_nivel6', type: 'int' },
        { name: 'cod_nivel7', type: 'int' }
    ]
});
