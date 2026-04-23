<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property \Carbon\CarbonImmutable $fecha
 * @property int|null $pep_id
 * @property string $estado
 * @property string|null $notas
 * @property \Carbon\CarbonImmutable|null $fecha_hora_evento
 * @property string|null $descripcion_evento
 * @property string|null $nombre_taller
 * @property \Carbon\CarbonImmutable|null $fecha_entrada_taller
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Colaborador> $colaboradores
 * @property-read int|null $colaboradores_count
 * @property-read \App\Models\Pep|null $pep
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Vehiculo> $vehiculos
 * @property-read int|null $vehiculos_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignacion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignacion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignacion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignacion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignacion whereDescripcionEvento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignacion whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignacion whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignacion whereFechaEntradaTaller($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignacion whereFechaHoraEvento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignacion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignacion whereNombreTaller($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignacion whereNotas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignacion wherePepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignacion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAsignacion {}
}

namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsignacionColaborador newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsignacionColaborador newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsignacionColaborador query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAsignacionColaborador {}
}

namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsignacionVehiculo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsignacionVehiculo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsignacionVehiculo query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAsignacionVehiculo {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $titulo
 * @property string|null $conteudo
 * @property string|null $imagem
 * @property string $cor
 * @property bool $ativo
 * @property int $ordem
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvisoTv newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvisoTv newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvisoTv query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvisoTv whereAtivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvisoTv whereConteudo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvisoTv whereCor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvisoTv whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvisoTv whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvisoTv whereImagem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvisoTv whereOrdem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvisoTv whereTitulo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvisoTv whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAvisoTv {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $numero_colaborador
 * @property string $nombre
 * @property string $apellido
 * @property string|null $telefono
 * @property string $denominacion_cargo
 * @property bool $activo
 * @property bool $visible_en_dashboard
 * @property string|null $motivo_baja
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador activos()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador inactivos()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereActivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereApellido($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereDenominacionCargo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereMotivoBaja($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereNumeroColaborador($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereTelefono($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereVisibleEnDashboard($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperColaborador {}
}

namespace App\Models{
/**
 * @property int $id
 * @property \Carbon\CarbonImmutable $fecha
 * @property bool $activo_tv
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiaPublicado newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiaPublicado newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiaPublicado query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiaPublicado whereActivoTv($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiaPublicado whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiaPublicado whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiaPublicado whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiaPublicado whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDiaPublicado {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pep> $peps
 * @property-read int|null $peps_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locacion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locacion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locacion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locacion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locacion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locacion whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locacion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLocacion {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int|null $locacion_id
 * @property int|null $tipo_trabajo_id
 * @property bool $activo
 * @property string|null $motivo_baja
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Locacion|null $locacion
 * @property-read \App\Models\TipoTrabajo|null $tipoTrabajo
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pep activos()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pep inactivos()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pep query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pep whereActivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pep whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pep whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pep whereLocacionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pep whereMotivoBaja($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pep whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pep whereTipoTrabajoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pep whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPep {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property string|null $color
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pep> $peps
 * @property-read int|null $peps_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoTrabajo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoTrabajo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoTrabajo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoTrabajo whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoTrabajo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoTrabajo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoTrabajo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoTrabajo whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTipoTrabajo {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property \Carbon\CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $marca
 * @property string $modelo
 * @property bool $activo
 * @property string|null $motivo_baja
 * @property string $matricula
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo activos()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo inactivos()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereActivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereMarca($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereMatricula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereModelo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereMotivoBaja($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperVehiculo {}
}

