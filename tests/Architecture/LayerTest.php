<?php

namespace App\Tests\Architecture;

use PHPat\Selector\Selector;
use PHPat\Selector\SelectorInterface;
use PHPat\Test\Builder\Rule;
use PHPat\Test\PHPat;

class LayerTest
{
    public function test_domain_does_not_depend_on_other_layers(): Rule
    {
        $domainNamespaces = $this->getAppNamespaces('Domain');
        $psrNamespaces = $this->getPsrNamespaces();
        $globalNamespace = $this->getGlobalNamespace();

        return PHPat::rule()
            ->classes($domainNamespaces)
            ->canOnlyDependOn()
            ->classes(
                $domainNamespaces,
                $psrNamespaces,
                $globalNamespace
            )
            ->because('Domain layer should not depend on other layers')
        ;
    }

    public function test_application_only_depends_on_itself_and_domain(): Rule
    {
        $domainNamespaces = $this->getAppNamespaces('Domain');
        $applicationNamespaces = $this->getAppNamespaces('Application');
        $psrNamespaces = $this->getPsrNamespaces();
        $globalNamespace = $this->getGlobalNamespace();

        return PHPat::rule()
            ->classes($applicationNamespaces)
            ->canOnlyDependOn()
            ->classes(
                $domainNamespaces,
                $applicationNamespaces,
                $psrNamespaces,
                $globalNamespace
            )
            ->because('Application layer should only depend on Domain and itself')
        ;
    }

    public function test_infrastructure_domain_does_not_depend_on_presentation(): Rule
    {
        $infrastructureNamespaces = $this->getAppNamespaces('Infrastructure');
        $presentationNamespaces = $this->getAppNamespaces('Presentation');

        return PHPat::rule()
            ->classes($infrastructureNamespaces)
            ->shouldNotDependOn()
            ->classes(
                $presentationNamespaces
            )
            ->because('Infrastructure layer should not depend on Presentation layer')
        ;
    }

    private function getAppNamespaces(string $layer): SelectorInterface
    {
        return Selector::inNamespace(
            sprintf('/^App\\\\.*\\\\%s\\\\.*/', $layer),
            true
        );
    }

    private function getPsrNamespaces(): SelectorInterface
    {
        return Selector::inNamespace('/^Psr\\\\.*/', true);
    }

    // Since PHPat doesn't fully support latest PHP 8 additions, we need to use this workaround to ignore new PHP built-in classes
    // -> It will also ignore user classes defined in the global namespace
    private function getGlobalNamespace(): SelectorInterface
    {
        /* @phpstan-ignore-next-line */
        return Selector::inNamespace('');
    }
}
