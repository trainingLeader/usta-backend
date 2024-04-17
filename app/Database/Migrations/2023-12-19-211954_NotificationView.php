<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NotificationView extends Migration
{
    public function up()
    {
        $this->db->query('
        CREATE  OR REPLACE VIEW notificationview AS
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
            h.para AS `to`,
            h.id AS idThread,
            (CASE
            WHEN
                ((TIMESTAMPDIFF(DAY,
                    NOW(),
                    CONCAT(`n`.`plazo`, " ", `n`.`hora`)) > `s`.`diasSemaforizacion`)
                    AND (`es`.`id` <> 5)
                    AND (`es`.`id` <> 3))
            THEN
                "Verde"
            WHEN
                ((TIMESTAMPDIFF(DAY,
                    NOW(),
                    CONCAT(`n`.`plazo`, " ", `n`.`hora`)) <= `s`.`diasSemaforizacion`)
                    AND (NOW() < CONCAT(`n`.`plazo`, " ", `n`.`hora`))
                    AND (`es`.`id` <> 5)
                    AND (`es`.`id` <> 3))
            THEN
                "Amarillo"
            WHEN
                ((NOW() > CONCAT(`n`.`plazo`, " ", `n`.`hora`))
                    AND (`es`.`id` <> 5)
                    AND (`es`.`id` <> 3))
            THEN
                "Rojo"
            WHEN ((`es`.`id` = 5) OR (`es`.`id` = 3)) THEN "Morado"
        END) AS `color`,
        (CASE
            WHEN
                ((TIMESTAMPDIFF(DAY,
                    NOW(),
                    CONCAT(`n`.`plazo`, " ", `n`.`hora`)) > `s`.`diasSemaforizacion`)
                    AND (`es`.`id` <> 5)
                    AND (`es`.`id` <> 3))
            THEN
                "A tiempo para gestionar el requerimiento"
            WHEN
                ((TIMESTAMPDIFF(DAY,
                    NOW(),
                    CONCAT(`n`.`plazo`, " ", `n`.`hora`)) <= `s`.`diasSemaforizacion`)
                    AND (NOW() < CONCAT(`n`.`plazo`, " ", `n`.`hora`))
                    AND (`es`.`id` <> 5)
                    AND (`es`.`id` <> 3))
            THEN
                CONCAT("Quedan ",
                        TIMESTAMPDIFF(DAY,
                            NOW(),
                            CONCAT(`n`.`plazo`, " ", `n`.`hora`)),
                        " días para gestionar el requerimiento")
            WHEN
                ((NOW() > CONCAT(n.plazo, " ", n.hora))
                    AND (es.id <> 5)
                    AND (es.id <> 3))
            THEN
                "El plazo ha vencido"
            WHEN ((es.id = 5) OR (es.id = 3)) THEN "Notificación cerrada"
        END) AS message,
        TIMESTAMPDIFF(DAY,
            NOW(),
            CONCAT(`n`.plazo, " ", `n`.hora)) AS daysToEnd,
        `n`.textoNotificacion AS body,
        es.nombreEstado AS status,
        es.id AS idStatus,
        n.recordatorioEnviado AS reminderSent,
        n.fechaEnvioMail AS sendDate,
        h.mensaje AS messageNotification,
        n.idEstadoNotificacionAgente AS idStatusAgente,
        f.nombreFormato As formatTypeName,
        tr.nombre AS requirementTypeName,
        cat.nombre AS categoryName
    FROM
        ((((modulonotificaciones n
        JOIN estadonotificacion es ON ((es.id = `n`.idEstadoNotificacion)))
        JOIN hilorespuestanotificacion h ON ((`h`.idNotificacion = `n`.id)))
        JOIN radicados r ON ((`n`.idRadicado = `r`.id)))
        LEFT JOIN formatos f ON f.id=n.idFormato
        LEFT JOIN tiporequerimiento tr ON tr.id=n.idRequerimiento
        LEFT JOIN categoria cat ON cat.id=n.idCategoria
        JOIN recordatorios s);            
        ');

    }

    public function down()
    {
        //
    }
}
