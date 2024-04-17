<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DraftsView extends Migration
{
    public function up()
    {
        $this->db->query('
        CREATE  OR REPLACE VIEW draftsview AS
        SELECT 
            n.id AS id,
            r.radicado AS caseNumber,
            n.asuntoNotificacion AS subject,
            n.plazo AS deadline,
            n.hora AS hour,
            n.entidad AS entity,
            n.idFormato AS formatType,
            n.idRequerimiento AS requirementType,
            n.anioReporte AS year,
            n.trimestre AS quarter,
            n.idCategoria AS category,
            n.sector AS sector,
            n.fechaCreacion AS createdDocument,
            n.adjunto AS attachedFile,
            n.observaciones AS observations,
            n.idTipoNotificacion AS idTipoNotificacion,
            n.para AS `to`,
            n.de AS `from`,
            n.archivoMasivo AS archivoMasivo,
            c.nombre AS campainName,
            (CASE
                WHEN
                    ((TIMESTAMPDIFF(DAY,
                        NOW(),
                        CONCAT(n.plazo, " ", n.hora)) > s.diasSemaforizacion)
                        AND (es.id <> 5))
                THEN
                    "Verde"
                WHEN
                    ((TIMESTAMPDIFF(DAY,
                        NOW(),
                        CONCAT(n.plazo, " ", n.hora)) <= s.diasSemaforizacion)
                        AND (NOW() < CONCAT(n.plazo, " ", n.hora))
                        AND (es.id <> 5))
                THEN
                    "Amarillo"
                WHEN
                    ((NOW() > CONCAT(n.plazo, " ", n.hora))
                        AND (es.id <> 5))
                THEN
                    "Rojo"
                WHEN (es.id = 5) THEN "Morado"
            END) AS color,
            (CASE
                WHEN
                    ((TIMESTAMPDIFF(DAY,
                        NOW(),
                        CONCAT(n.plazo, " ", n.hora)) > s.diasSemaforizacion)
                        AND (es.id <> 5))
                THEN
                    "A tiempo para gestionar el requerimiento"
                WHEN
                    ((TIMESTAMPDIFF(DAY,
                        NOW(),
                        CONCAT(n.plazo, " ", n.hora)) <= s.diasSemaforizacion)
                        AND (NOW() < CONCAT(n.plazo, " ", n.hora))
                        AND (es.id <> 5))
                THEN
                    CONCAT("Quedan ",
                            TIMESTAMPDIFF(DAY,
                                NOW(),
                                CONCAT(n.plazo, " ", n.hora)),
                            " días para gestionar el requerimiento")
                WHEN
                    ((NOW() > CONCAT(n.plazo, " ", n.hora))
                        AND (es.id <> 5))
                THEN
                    "El plazo ha vencido"
                WHEN (es.id = 5) THEN "Notificación cerrada"
            END) AS message,
            TIMESTAMPDIFF(DAY,
                NOW(),
                CONCAT(n.plazo, " ", n.hora)) AS daysToEnd,
            n.textoNotificacion AS body,
            es.nombreEstado AS status,
            es.id AS idStatus
        FROM
        ((((borradores n
        JOIN estadonotificacion es ON ((es.id = n.idEstadoNotificacion)))
        LEFT JOIN radicados r ON ((n.idRadicado = r.id)))
        LEFT JOIN campania c ON ((c.id = n.idCampania)))
        JOIN recordatorios s);            
        ');

    }

    public function down()
    {
        //
    }
}
