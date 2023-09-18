<?php



namespace Aventi\QuickOrder\Setup;


use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 *
 * @package Aventi\QuickOrder\Module\Setup
 */
class InstallData implements InstallDataInterface
{
    /*
     *
     */
    private $blockFactory;

    /**
     * InstallData constructor.
     *
     * @param BlockFactory $blockFactory
     */
    public function __construct(BlockFactory $blockFactory)
    {
        $this->blockFactory = $blockFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @method
     * date 30/05/19/03:24 PM
     * @author Carlos Hernan Aguilar Hurtado <caguilar@aventi.co>
     * @throws \Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $cmsBlockData = [
            'title' => 'Orden Rápida',
            'identifier' => 'aventi_quick_order',
            'content' => "<h1>ORDEN RÁPIDA</h1>
                        <p>Introduzca el nombre o código del producto en el primer campo. Los productos relacionados serán mostrados junto a los productos. Seleccione el producto deseado de la
lista. En el campo QTY introduzca el número de artículos que desea ordenar. Si necesita mas líneas, presione el botón de Agregar Filas. Para eliminar una línea, presione el
botón de remover filas. Al completar, presione el botón de Agregar todos los artículos</p>

",
            'is_active' => 1,
            'stores' => [0],
            'sort_order' => 0
        ];

        $this->blockFactory->create()->setData($cmsBlockData)->save();
    }
}