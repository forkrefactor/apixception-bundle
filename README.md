# Pccomponentes Apixception bundle

El objetivo de esta librería es ofrecer una pequeña ayuda al programador a la hora de renderizar las excepciones que llegan al controlador, como respuestas del API.
Está integrado usando el evento `Kernel.exception` del Kernel de Symfony Framework v4.
Las respuestas serán en formato JSON (`Symfony\Component\HttpFoundation\JsonResponse`).

## Instalación

1. Descarga e instala el vendor usando [composer](https://getcomposer.org/).

    ```bash
    $ composer require pccomponentes/apixception-bundle
    ```

2. Añade el bundle en `config\bundles.php`. Por ejemplo:

    ```php
    <?php
    
    return [
        Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],  
        PcComponentes\Apixception\ApixceptionBundle::class => ['all' => true]  
    ];
    ```

3. Escribe el fichero de configuración del bundle indicando las excepciones que deseas capturar, y su transformación para generar la respuesta.
   Para ello, crea un archivo con nombre `apixception.yaml` en la ruta `config/packages`.
   Un ejemplo de su contenido es:

    ```yaml
    apixception:
      - exception: PcComponentes\Ddd\Domain\Exception\NotFoundException
        transformer: PcComponentes\Apixception\Core\Transformer\JsonSerializableTransformer
        http_code: 404
      - exception: PcComponentes\Ddd\Domain\Exception\ExistsException
        transformer: PcComponentes\Apixception\Core\Transformer\JsonSerializableTransformer
        http_code: 409
      - exception: PcComponentes\Ddd\Domain\Exception\LogicException
        transformer: PcComponentes\Apixception\Core\Transformer\JsonSerializableTransformer
        http_code: 409
      - exception: \Throwable
        transformer: PcComponentes\Apixception\Core\Transformer\NoSerializableTransformer
        http_code: 500
    ```

    Este archivo se modificará para añadir o quitar las reglas que sean necesarias para el proyecto en el que se use.

## Configuración

Este archivo debe contener un listado de reglas. Cada regla debe tener:

 - `exception`: Nombre de la clase o interface, namespace incluído, que representa el tipo de la excepción que se quiere capturar.
 - `http_code`: Código HTTP que devolverá el objeto `Symfony\Component\HttpFoundation\JsonResponse`.
 - `transformer`: Clase con namespace incluído, que transformará la excepción en un array de datos serializables por el objeto `Symfony\Component\HttpFoundation\JsonResponse`.

## Creación de un nuevo transformador

Aunque la aplicación proporciona un par de transformadores básicos, está abierto para que cada proyecto pueda inyectar sus propios transformadores.
Las excepción deben implementar la interfaz `\Throwable` o de heredar de clases que ya lo hagan.
Los transformadores son clases que deben heredar de la clase `PcComponentes\Apixception\Core\Transformer\ExceptionTransformer`.
Por cuestiones de simplicidad, no se permite inyectar dependencias, así que heredar de esta clase implica tener un constructor vacío y contener lógica de transformación ligera.

Un ejemplo de un transformador propio sería:

```php
<?php
namespace MyApp\Transformers;

use PcComponentes\Apixception\Core\Transformer\ExceptionTransformer;

class CustomTransformer extends ExceptionTransformer
{
	public function transform(\Throwable $exception): array
	{
		return [
			'exception' => \get_class($exception),
			'message' => $exception->getMessage(),
		];
	}
}
```

Si llegara una excepción personalizada, con métodos únicos, puedes usarlas.
Asegúrate que en la configuración garantice que a tu transformador sólo llegan excepciones del tipo que esperas, o haz que tu transformador actúe en consecuencia.

## Transformadores disponibles

La librería proporciona dos transformadores que puedes usar desde el primer momento.
 - `PcComponentes\Apixception\Core\Transformer\NoSerializableTransformer`:
    Este transformador es capaz de renderizar cualquier tipo de excepción.
    En la respuesta meterá un objeto JSON con las propiedades  `exception` con el nombre de la excepción, y `message`, con el mensaje de la excepción.
 - `PcComponentes\Apixception\Core\Transformer\JsonSerializableTransformer`:
    Este transformador será capaz de renderizar cualquier excepción que implemente la interfaz `\JsonSerializable`.
    El array que devuelva dicho método, será lo que se incluya en la respuesta.

## Excepciones

Como se ha comentado anteriormente, esta librería acepta excepciones de todo tipo.
