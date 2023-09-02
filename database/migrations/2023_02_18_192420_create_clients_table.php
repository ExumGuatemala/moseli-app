<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('municipios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('departamento_id');
            $table->string('name');

            $table->foreign('departamento_id')->references('id')->on('departamentos');
        });

		Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('key');
            $table->string('address');
			$table->unsignedBigInteger('municipio_id')->nullable();
            $table->string('phone1');
            $table->string('phone2')->nullable();
            $table->timestamps();

			$table->foreign('municipio_id')->references('id')->on('municipios');
        });

        //We will fill the departamento and municipio tables. just for Guatemala
        DB::update("INSERT INTO departamentos VALUES (null, 'Alta Verapaz');");
        DB::update("INSERT INTO departamentos VALUES (null, 'Baja Verapaz');");
        DB::update("INSERT INTO departamentos VALUES (null, 'Chimaltenango');");
        DB::update("INSERT INTO departamentos VALUES (null, 'Chiquimula');");
        DB::update("INSERT INTO departamentos VALUES (null, 'El Progreso');");
        DB::update("INSERT INTO departamentos VALUES (null, 'Escuintla');");
        DB::update("INSERT INTO departamentos VALUES (null, 'Guatemala');");
        DB::update("INSERT INTO departamentos VALUES (null, 'Huehuetenango');");
        DB::update("INSERT INTO departamentos VALUES (null, 'Izabal');");
        DB::update("INSERT INTO departamentos VALUES (null, 'Jalapa');");
        DB::update("INSERT INTO departamentos VALUES (null, 'Jutiapa');");
        DB::update("INSERT INTO departamentos VALUES (null, 'Petén');");
        DB::update("INSERT INTO departamentos VALUES (null, 'Quetzaltenango');");
        DB::update("INSERT INTO departamentos VALUES (null, 'Quiché');");
        DB::update("INSERT INTO departamentos VALUES (null, 'Retalhuleu');");
        DB::update("INSERT INTO departamentos VALUES (null, 'Sacatepéquez');");
        DB::update("INSERT INTO departamentos VALUES (null, 'San Marcos');");
        DB::update("INSERT INTO departamentos VALUES (null, 'Santa Rosa');");
        DB::update("INSERT INTO departamentos VALUES (null, 'Solola');");
        DB::update("INSERT INTO departamentos VALUES (null, 'Suchitepéquez');");
        DB::update("INSERT INTO departamentos VALUES (null, 'Totonicapán');");
        DB::update("INSERT INTO departamentos VALUES (null, 'Zacapa');");

        DB::update("INSERT INTO `municipios`
        (`id`, `name`, `departamento_id`)
        VALUES

          -- Alta Verapaz
        (NULL, 'Chahal', 1),
        (NULL, 'Chisec', 1),
        (NULL, 'Cobán', 1),
        (NULL, 'Fray Bartolomé de las Casas', 1),
        (NULL, 'La Tinta', 1),
        (NULL, 'Lanquín', 1),
        (NULL, 'Panzós', 1),
        (NULL, 'Raxruhá', 1),
        (NULL, 'San Cristóbal Verapaz', 1),
        (NULL, 'San Juan Chamelco', 1),
        (NULL, 'San Pedro Carchá', 1),
        (NULL, 'Santa Cruz Verapaz', 1),
        (NULL, 'Santa María Cahabón', 1),
        (NULL, 'Senahú', 1),
        (NULL, 'Tamahú', 1),
        (NULL, 'Tactic', 1),
        (NULL, 'Tucurú', 1),

          -- Baja Verapaz
        (NULL, 'Cubulco', 2),
        (NULL, 'Granados', 2),
        (NULL, 'Purulhá', 2),
        (NULL, 'Rabinal', 2),
        (NULL, 'Salamá', 2),
        (NULL, 'San Jerónimo', 2),
        (NULL, 'San Miguel Chicaj', 2),
        (NULL, 'Santa Cruz el Chol', 2),

          -- Chimaltenango
        (NULL, 'Acatenango', 3),
        (NULL, 'Chimaltenango', 3),
        (NULL, 'El Tejar', 3),
        (NULL, 'Parramos', 3),
        (NULL, 'Patzicía', 3),
        (NULL, 'Patzún', 3),
        (NULL, 'Pochuta', 3),
        (NULL, 'San Andrés Itzapa', 3),
        (NULL, 'San José Poaquíl', 3),
        (NULL, 'San Juan Comalapa', 3),
        (NULL, 'San Martín Jilotepeque', 3),
        (NULL, 'Santa Apolonia', 3),
        (NULL, 'Santa Cruz Balanyá', 3),
        (NULL, 'Tecpán', 3),
        (NULL, 'Yepocapa', 3),
        (NULL, 'Zaragoza', 3),

          -- Chiquimula
        (NULL, 'Camotán', 4),
        (NULL, 'Chiquimula', 4),
        (NULL, 'Concepción Las Minas', 4),
        (NULL, 'Esquipulas', 4),
        (NULL, 'Ipala', 4),
        (NULL, 'Jocotán', 4),
        (NULL, 'Olopa', 4),
        (NULL, 'Quezaltepeque', 4),
        (NULL, 'San Jacinto', 4),
        (NULL, 'San José la Arada', 4),
        (NULL, 'San Juan Ermita', 4),

          -- El Progreso
        (NULL, 'El Jícaro', 5),
        (NULL, 'Guastatoya', 5),
        (NULL, 'Morazán', 5),
        (NULL, 'San Agustín Acasaguastlán', 5),
        (NULL, 'San Antonio La Paz', 5),
        (NULL, 'San Cristóbal Acasaguastlán', 5),
        (NULL, 'Sanarate', 5),
        (NULL, 'Sansare', 5),

          -- Escuintla
        (NULL, 'Escuintla', 6),
        (NULL, 'Guanagazapa', 6),
        (NULL, 'Iztapa', 6),
        (NULL, 'La Democracia', 6),
        (NULL, 'La Gomera', 6),
        (NULL, 'Masagua', 6),
        (NULL, 'Nueva Concepción', 6),
        (NULL, 'Palín', 6),
        (NULL, 'San José', 6),
        (NULL, 'San Vicente Pacaya', 6),
        (NULL, 'Santa Lucía Cotzumalguapa', 6),
        (NULL, 'Siquinalá', 6),
        (NULL, 'Tiquisate', 6),

          -- Guatemala
        (NULL, 'Amatitlán', 7),
        (NULL, 'Chinautla', 7),
        (NULL, 'Chuarrancho', 7),
        (NULL, 'Guatemala', 7),
        (NULL, 'Fraijanes', 7),
        (NULL, 'Mixco', 7),
        (NULL, 'Palencia', 7),
        (NULL, 'San José del Golfo', 7),
        (NULL, 'San José Pinula', 7),
        (NULL, 'San Juan Sacatepéquez', 7),
        (NULL, 'San Miguel Petapa', 7),
        (NULL, 'San Pedro Ayampuc', 7),
        (NULL, 'San Pedro Sacatepéquez', 7),
        (NULL, 'San Raymundo', 7),
        (NULL, 'Santa Catarina Pinula', 7),
        (NULL, 'Villa Canales', 7),
        (NULL, 'Villa Nueva', 7),

          -- Huehuetenango
        (NULL, 'Aguacatán', 8),
        (NULL, 'Chiantla', 8),
        (NULL, 'Colotenango', 8),
        (NULL, 'Concepción Huista', 8),
        (NULL, 'Cuilco', 8),
        (NULL, 'Huehuetenango', 8),
        (NULL, 'Jacaltenango', 8),
        (NULL, 'La Democracia', 8),
        (NULL, 'La Libertad', 8),
        (NULL, 'Malacatancito', 8),
        (NULL, 'Nentón', 8),
        (NULL, 'San Antonio Huista', 8),
        (NULL, 'San Gaspar Ixchil', 8),
        (NULL, 'San Ildefonso Ixtahuacán', 8),
        (NULL, 'San Juan Atitán', 8),
        (NULL, 'San Juan Ixcoy', 8),
        (NULL, 'San Mateo Ixtatán', 8),
        (NULL, 'San Miguel Acatán', 8),
        (NULL, 'San Pedro Nécta', 8),
        (NULL, 'San Pedro Soloma', 8),
        (NULL, 'San Rafael La Independencia', 8),
        (NULL, 'San Rafael Pétzal', 8),
        (NULL, 'San Sebastián Coatán', 8),
        (NULL, 'San Sebastián Huehuetenango', 8),
        (NULL, 'Santa Ana Huista', 8),
        (NULL, 'Santa Bárbara', 8),
        (NULL, 'Santa Cruz Barillas', 8),
        (NULL, 'Santa Eulalia', 8),
        (NULL, 'Santiago Chimaltenango', 8),
        (NULL, 'Tectitán', 8),
        (NULL, 'Todos Santos Cuchumatán', 8),
        (NULL, 'Unión Cantinil', 8),

          -- Izabal
        (NULL, 'El Estor', 9),
        (NULL, 'Livingston', 9),
        (NULL, 'Los Amates', 9),
        (NULL, 'Morales', 9),
        (NULL, 'Puerto Barrios', 9),

          -- Jalapa
        (NULL, 'Jalapa', 10),
        (NULL, 'Mataquescuintla', 10),
        (NULL, 'Monjas', 10),
        (NULL, 'San Carlos Alzatate', 10),
        (NULL, 'San Luis Jilotepeque', 10),
        (NULL, 'San Manuel Chaparrón', 10),
        (NULL, 'San Pedro Pinula', 10),

          -- Jutiapa
        (NULL, 'Agua Blanca', 11),
        (NULL, 'Asunción Mita', 11),
        (NULL, 'Atescatempa', 11),
        (NULL, 'Comapa', 11),
        (NULL, 'Conguaco', 11),
        (NULL, 'El Adelanto', 11),
        (NULL, 'El Progreso', 11),
        (NULL, 'Jalpatagua', 11),
        (NULL, 'Jerez', 11),
        (NULL, 'Jutiapa', 11),
        (NULL, 'Moyuta', 11),
        (NULL, 'Pasaco', 11),
        (NULL, 'Quesada', 11),
        (NULL, 'San José Acatempa', 11),
        (NULL, 'Santa Catarina Mita', 11),
        (NULL, 'Yupiltepeque', 11),
        (NULL, 'Zapotitlán', 11),

          -- Petén
        (NULL, 'Dolores', 12),
        (NULL, 'El Chal', 12),
        (NULL, 'Ciudad Flores', 12),
        (NULL, 'La Libertad', 12),
        (NULL, 'Las Cruces', 12),
        (NULL, 'Melchor de Mencos', 12),
        (NULL, 'Poptún', 12),
        (NULL, 'San Andrés', 12),
        (NULL, 'San Benito', 12),
        (NULL, 'San Francisco', 12),
        (NULL, 'San José', 12),
        (NULL, 'San Luis', 12),
        (NULL, 'Santa Ana', 12),
        (NULL, 'Sayaxché', 12),

          -- Quetzaltenango
        (NULL, 'Almolonga', 13),
        (NULL, 'Cabricán', 13),
        (NULL, 'Cajolá', 13),
        (NULL, 'Cantel', 13),
        (NULL, 'Coatepeque', 13),
        (NULL, 'Colomba Costa Cuca', 13),
        (NULL, 'Concepción Chiquirichapa', 13),
        (NULL, 'El Palmar', 13),
        (NULL, 'Flores Costa Cuca', 13),
        (NULL, 'Génova', 13),
        (NULL, 'Huitán', 13),
        (NULL, 'La Esperanza', 13),
        (NULL, 'Olintepeque', 13),
        (NULL, 'Palestina de Los Altos', 13),
        (NULL, 'Quetzaltenango', 13),
        (NULL, 'Salcajá', 13),
        (NULL, 'San Carlos Sija', 13),
        (NULL, 'San Francisco La Unión', 13),
        (NULL, 'San Juan Ostuncalco', 13),
        (NULL, 'San Martín Sacatepéquez', 13),
        (NULL, 'San Mateo', 13),
        (NULL, 'San Miguel Sigüilá', 13),
        (NULL, 'Sibilia', 13),
        (NULL, 'Zunil', 13),

          -- Quiché
        (NULL, 'Canillá', 14),
        (NULL, 'Chajul', 14),
        (NULL, 'Chicamán', 14),
        (NULL, 'Chiché', 14),
        (NULL, 'Chichicastenango', 14),
        (NULL, 'Chinique', 14),
        (NULL, 'Cunén', 14),
        (NULL, 'Ixcán Playa Grande', 14),
        (NULL, 'Joyabaj', 14),
        (NULL, 'Nebaj', 14),
        (NULL, 'Pachalum', 14),
        (NULL, 'Patzité', 14),
        (NULL, 'Sacapulas', 14),
        (NULL, 'San Andrés Sajcabajá', 14),
        (NULL, 'San Antonio Ilotenango', 14),
        (NULL, 'San Bartolomé Jocotenango', 14),
        (NULL, 'San Juan Cotzal', 14),
        (NULL, 'San Pedro Jocopilas', 14),
        (NULL, 'Santa Cruz del Quiché', 14),
        (NULL, 'Uspantán', 14),
        (NULL, 'Zacualpa', 14),

          -- Retalhuleu
        (NULL, 'Champerico', 15),
        (NULL, 'El Asintal', 15),
        (NULL, 'Nuevo San Carlos', 15),
        (NULL, 'Retalhuleu', 15),
        (NULL, 'San Andrés Villa Seca', 15),
        (NULL, 'San Felipe Reu', 15),
        (NULL, 'San Martín Zapotitlán', 15),
        (NULL, 'San Sebastián', 15),
        (NULL, 'Santa Cruz Muluá', 15),

          -- Sacatepéquez
        (NULL, 'Alotenango', 16),
        (NULL, 'Ciudad Vieja', 16),
        (NULL, 'Jocotenango', 16),
        (NULL, 'Antigua Guatemala', 16),
        (NULL, 'Magdalena Milpas Altas', 16),
        (NULL, 'Pastores', 16),
        (NULL, 'San Antonio Aguas Calientes', 16),
        (NULL, 'San Bartolomé Milpas Altas', 16),
        (NULL, 'San Lucas Sacatepéquez', 16),
        (NULL, 'San Miguel Dueñas', 16),
        (NULL, 'Santa Catarina Barahona', 16),
        (NULL, 'Santa Lucía Milpas Altas', 16),
        (NULL, 'Santa María de Jesús', 16),
        (NULL, 'Santiago Sacatepéquez', 16),
        (NULL, 'Santo Domingo Xenacoj', 16),
        (NULL, 'Sumpango', 16),

          -- San Marcos
        (NULL, 'Ayutla', 17),
        (NULL, 'Catarina', 17),
        (NULL, 'Comitancillo', 17),
        (NULL, 'Concepción Tutuapa', 17),
        (NULL, 'El Quetzal', 17),
        (NULL, 'El Tumbador', 17),
        (NULL, 'Esquipulas Palo Gordo', 17),
        (NULL, 'Ixchiguán', 17),
        (NULL, 'La Blanca', 17),
        (NULL, 'La Reforma', 17),
        (NULL, 'Malacatán', 17),
        (NULL, 'Nuevo Progreso', 17),
        (NULL, 'Ocós', 17),
        (NULL, 'Pajapita', 17),
        (NULL, 'Río Blanco', 17),
        (NULL, 'San Antonio Sacatepéquez', 17),
        (NULL, 'San Cristóbal Cucho', 17),
        (NULL, 'San José El Rodeo', 17),
        (NULL, 'San José Ojetenam', 17),
        (NULL, 'San Lorenzo', 17),
        (NULL, 'San Marcos', 17),
        (NULL, 'San Miguel Ixtahuacán', 17),
        (NULL, 'San Pablo', 17),
        (NULL, 'San Pedro Sacatepéquez', 17),
        (NULL, 'San Rafael Pie de la Cuesta', 17),
        (NULL, 'Sibinal', 17),
        (NULL, 'Sipacapa', 17),
        (NULL, 'Tacaná', 17),
        (NULL, 'Tajumulco', 17),
        (NULL, 'Tejutla', 17),

        -- Santa Rosa
        (NULL, 'Barberena', 18),
        (NULL, 'Casillas', 18),
        (NULL, 'Chiquimulilla', 18),
        (NULL, 'Cuilapa', 18),
        (NULL, 'Guazacapán', 18),
        (NULL, 'Nueva Santa Rosa', 18),
        (NULL, 'Oratorio', 18),
        (NULL, 'Pueblo Nuevo Viñas', 18),
        (NULL, 'San Juan Tecuaco', 18),
        (NULL, 'San Rafael las Flores', 18),
        (NULL, 'Santa Cruz Naranjo', 18),
        (NULL, 'Santa María Ixhuatán', 18),
        (NULL, 'Santa Rosa de Lima', 18),
        (NULL, 'Taxisco', 18),

        -- Sololá
        (NULL, 'Concepción', 19),
        (NULL, 'Nahualá', 19),
        (NULL, 'Panajachel', 19),
        (NULL, 'San Andrés Semetabaj', 19),
        (NULL, 'San Antonio Palopó', 19),
        (NULL, 'San José Chacayá', 19),
        (NULL, 'San Juan La Laguna', 19),
        (NULL, 'San Lucas Tolimán', 19),
        (NULL, 'San Marcos La Laguna', 19),
        (NULL, 'San Pablo La Laguna', 19),
        (NULL, 'San Pedro La Laguna', 19),
        (NULL, 'Santa Catarina Ixtahuacán', 19),
        (NULL, 'Santa Catarina Palopó', 19),
        (NULL, 'Santa Clara La Laguna', 19),
        (NULL, 'Santa Cruz La Laguna', 19),
        (NULL, 'Santa Lucía Utatlán', 19),
        (NULL, 'Santa María Visitación', 19),
        (NULL, 'Santiago Atitlán', 19),
        (NULL, 'Sololá', 19),

        -- Suchitepéquez
        (NULL, 'Chicacao', 20),
        (NULL, 'Cuyotenango', 20),
        (NULL, 'Mazatenango', 20),
        (NULL, 'Patulul', 20),
        (NULL, 'Pueblo Nuevo', 20),
        (NULL, 'Río Bravo', 20),
        (NULL, 'Samayac', 20),
        (NULL, 'San Antonio Suchitepéquez', 20),
        (NULL, 'San Bernardino', 20),
        (NULL, 'San Francisco Zapotitlán', 20),
        (NULL, 'San Gabriel', 20),
        (NULL, 'San José El Idolo', 20),
        (NULL, 'San José La Maquina', 20),
        (NULL, 'San Juan Bautista', 20),
        (NULL, 'San Lorenzo', 20),
        (NULL, 'San Miguel Panán', 20),
        (NULL, 'San Pablo Jocopilas', 20),
        (NULL, 'Santa Bárbara', 20),
        (NULL, 'Santo Domingo Suchitepéquez', 20),
        (NULL, 'Santo Tomás La Unión', 20),
        (NULL, 'Zunilito', 20),

        -- Totonicapán
        (NULL, 'Momostenango', 21),
        (NULL, 'San Andrés Xecul', 21),
        (NULL, 'San Bartolo', 21),
        (NULL, 'San Cristóbal Totonicapán', 21),
        (NULL, 'San Francisco El Alto', 21),
        (NULL, 'Santa Lucía La Reforma', 21),
        (NULL, 'Santa María Chiquimula', 21),
        (NULL, 'Totonicapán', 21),

        -- Zacapa
        (NULL, 'Cabañas', 22),
        (NULL, 'Estanzuela', 22),
        (NULL, 'Gualán', 22),
        (NULL, 'Huité', 22),
        (NULL, 'La Unión', 22),
        (NULL, 'Río Hondo', 22),
        (NULL, 'San Diego', 22),
        (NULL, 'San Jorge', 22),
        (NULL, 'Teculután', 22),
        (NULL, 'Usumatlán', 22),
        (NULL, 'Zacapa', 22);");


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
		Schema::dropIfExists('municipios');
        Schema::dropIfExists('departamentos');
    }
};
