<?php

declare(strict_types=1);

namespace Myohanhtet\ServiceLayer\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

class MakeServiceCommand extends GeneratorCommand
{
    protected $name = 'make:service';
    protected $description = 'Create a new service interface and implementation';
    protected $type = 'Service';

    protected function getStub(): string
    {
        return __DIR__ . '/../stubs/service-impl.stub';
    }

    protected function getInterfaceStub(): string
    {
        return __DIR__ . '/../stubs/service-interface.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Services\Impl';
    }

    protected function getInterfaceNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Services';
    }

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the service'],
        ];
    }

    public function handle()
    {
        $serviceName = $this->getNameInput();
        $interfaceClass = $this->getInterfaceNamespace($this->rootNamespace()) . '\\' . $serviceName;
        $interfacePath = $this->getPath($interfaceClass);

        // Create interface
        if (!$this->files->exists($interfacePath)) {
            $this->makeDirectory($interfacePath);
            $this->files->put($interfacePath, $this->buildInterfaceClass($serviceName));
            $this->info('Service interface created successfully.');
        } else {
            $this->warn('Service interface already exists!');
        }

        // Generate implementation using Laravel's built-in handling
        parent::handle();

        // Rename implementation file to YourServiceImpl.php
        $originalImplPath = $this->getPath($this->qualifyClass($serviceName));
        $correctImplPath = str_replace(
            $serviceName . '.php',
            $serviceName . 'Impl.php',
            $originalImplPath
        );

        if ($originalImplPath !== $correctImplPath && $this->files->exists($originalImplPath)) {
            $this->files->move($originalImplPath, $correctImplPath);
        }

        $this->bindInterfaceToImplementation($serviceName);
    }

    protected function buildInterfaceClass($name): string
    {
        $stub = $this->files->get($this->getInterfaceStub());

        return str_replace(
            ['{{ interface }}', '{{ namespace }}'],
            [$name, str_replace('\\\\', '\\', $this->getInterfaceNamespace($this->rootNamespace()))],
            $stub
        );
    }

    protected function buildClass($name): string
    {
        $stub = $this->files->get($this->getStub());

        return str_replace(
            ['{{ class }}', '{{ interface }}', '{{ namespace }}'],
            [
                $this->getNameInput() . 'Impl',
                $this->getNameInput(),
                str_replace('\\\\', '\\', $this->getDefaultNamespace($this->rootNamespace()))
            ],
            $stub
        );
    }

    protected function bindInterfaceToImplementation($name): void
    {
        $interface = 'App\\Services\\' . $name;
        $implementation = 'App\\Services\\Impl\\' . $name . 'Impl';

        $providerPath = app_path('Providers/AppServiceProvider.php');

        if (!file_exists($providerPath)) {
            $this->warn('AppServiceProvider not found. Skipping binding.');
            return;
        }

        $providerContent = file_get_contents($providerPath);
        $bindingLine = "\$this->app->bind(\\{$interface}::class, \\{$implementation}::class);";

        if (str_contains($providerContent, $bindingLine)) {
            $this->warn('Binding already exists in AppServiceProvider.');
            return;
        }

        $providerContent = preg_replace(
            '/public function register\(\)\s*\{\n/',
            "public function register()\n    {\n        {$bindingLine}\n",
            $providerContent
        );

        file_put_contents($providerPath, $providerContent);

        $this->info("Bound {$interface} to {$implementation} in AppServiceProvider.");
    }
}
