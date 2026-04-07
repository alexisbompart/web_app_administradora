<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WelcomeSlider;
use App\Models\WelcomeService;
use App\Models\WelcomeResidence;
use App\Models\WelcomeProduct;
use App\Models\WelcomePopup;
use App\Models\WelcomeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WelcomeContentController extends Controller
{
    public function index()
    {
        $sliders = WelcomeSlider::orderBy('orden')->get();
        $services = WelcomeService::orderBy('orden')->get();
        $residences = WelcomeResidence::orderBy('orden')->get();
        $products = WelcomeProduct::orderBy('orden')->get();
        $popups = WelcomePopup::latest()->get();
        $settings = WelcomeSetting::all()->groupBy('seccion');

        return view('admin.welcome-content', compact('sliders', 'services', 'residences', 'products', 'popups', 'settings'));
    }

    // ============ SLIDERS ============

    public function storeSlider(Request $request)
    {
        $request->validate([
            'titulo' => 'nullable|string|max:255',
            'subtitulo' => 'nullable|string|max:255',
            'imagen' => 'required|image|max:4096',
            'boton_texto' => 'nullable|string|max:100',
            'boton_url' => 'nullable|string|max:255',
            'orden' => 'integer',
        ]);

        $data = $request->except('imagen');
        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('welcome/sliders', 'public');
        }
        $data['activo'] = $request->has('activo');

        WelcomeSlider::create($data);

        return back()->with('success', 'Slide agregado correctamente.');
    }

    public function updateSlider(Request $request, WelcomeSlider $slider)
    {
        $request->validate([
            'titulo' => 'nullable|string|max:255',
            'subtitulo' => 'nullable|string|max:255',
            'imagen' => 'nullable|image|max:4096',
            'boton_texto' => 'nullable|string|max:100',
            'boton_url' => 'nullable|string|max:255',
            'orden' => 'integer',
        ]);

        $data = $request->except(['imagen', '_token', '_method']);
        if ($request->hasFile('imagen')) {
            if ($slider->imagen) {
                Storage::disk('public')->delete($slider->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('welcome/sliders', 'public');
        }
        $data['activo'] = $request->has('activo');

        $slider->update($data);

        return back()->with('success', 'Slide actualizado correctamente.');
    }

    public function destroySlider(WelcomeSlider $slider)
    {
        if ($slider->imagen) {
            Storage::disk('public')->delete($slider->imagen);
        }
        $slider->delete();

        return back()->with('success', 'Slide eliminado correctamente.');
    }

    // ============ SERVICES ============

    public function storeService(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'icono' => 'required|string|max:100',
            'color_icono' => 'nullable|string|max:20',
            'orden' => 'integer',
        ]);

        $data = $request->all();
        $data['activo'] = $request->has('activo');

        WelcomeService::create($data);

        return back()->with('success', 'Servicio agregado correctamente.');
    }

    public function updateService(Request $request, WelcomeService $service)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'icono' => 'required|string|max:100',
            'color_icono' => 'nullable|string|max:20',
            'orden' => 'integer',
        ]);

        $data = $request->except(['_token', '_method']);
        $data['activo'] = $request->has('activo');

        $service->update($data);

        return back()->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroyService(WelcomeService $service)
    {
        $service->delete();
        return back()->with('success', 'Servicio eliminado correctamente.');
    }

    // ============ RESIDENCES ============

    public function storeResidence(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'imagen' => 'required|image|max:4096',
            'ubicacion' => 'nullable|string|max:255',
            'orden' => 'integer',
        ]);

        $data = $request->except('imagen');
        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('welcome/residencias', 'public');
        }
        $data['activo'] = $request->has('activo');

        WelcomeResidence::create($data);

        return back()->with('success', 'Residencia agregada correctamente.');
    }

    public function updateResidence(Request $request, WelcomeResidence $residence)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'imagen' => 'nullable|image|max:4096',
            'ubicacion' => 'nullable|string|max:255',
            'orden' => 'integer',
        ]);

        $data = $request->except(['imagen', '_token', '_method']);
        if ($request->hasFile('imagen')) {
            if ($residence->imagen) {
                Storage::disk('public')->delete($residence->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('welcome/residencias', 'public');
        }
        $data['activo'] = $request->has('activo');

        $residence->update($data);

        return back()->with('success', 'Residencia actualizada correctamente.');
    }

    public function destroyResidence(WelcomeResidence $residence)
    {
        if ($residence->imagen) {
            Storage::disk('public')->delete($residence->imagen);
        }
        $residence->delete();

        return back()->with('success', 'Residencia eliminada correctamente.');
    }

    // ============ PRODUCTS ============

    public function storeProduct(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'slogan' => 'nullable|string|max:255',
            'descripcion' => 'required|string',
            'detalle' => 'nullable|string',
            'icono' => 'required|string|max:100',
            'color' => 'nullable|string|max:20',
            'orden' => 'integer',
        ]);

        $data = $request->only(['titulo', 'slogan', 'descripcion', 'detalle', 'icono', 'color', 'orden']);
        $data['activo'] = $request->has('activo');

        WelcomeProduct::create($data);

        return back()->with('success', 'Producto/Servicio agregado correctamente.');
    }

    public function updateProduct(Request $request, WelcomeProduct $product)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'slogan' => 'nullable|string|max:255',
            'descripcion' => 'required|string',
            'detalle' => 'nullable|string',
            'icono' => 'required|string|max:100',
            'color' => 'nullable|string|max:20',
            'orden' => 'integer',
        ]);

        $data = $request->only(['titulo', 'slogan', 'descripcion', 'detalle', 'icono', 'color', 'orden']);
        $data['activo'] = $request->has('activo');

        $product->update($data);

        return back()->with('success', 'Producto/Servicio actualizado correctamente.');
    }

    public function destroyProduct(WelcomeProduct $product)
    {
        $product->delete();
        return back()->with('success', 'Producto/Servicio eliminado correctamente.');
    }

    // ============ POPUPS ============

    public function storePopup(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',
            'imagen' => 'nullable|image|max:4096',
            'boton_texto' => 'nullable|string|max:100',
            'boton_url' => 'nullable|string|max:255',
            'icono' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:20',
        ]);

        $data = $request->except('imagen');
        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('welcome/popups', 'public');
        }
        $data['activo'] = $request->has('activo');

        WelcomePopup::create($data);

        return back()->with('success', 'Ventana emergente creada correctamente.');
    }

    public function updatePopup(Request $request, WelcomePopup $popup)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',
            'imagen' => 'nullable|image|max:4096',
            'boton_texto' => 'nullable|string|max:100',
            'boton_url' => 'nullable|string|max:255',
            'icono' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:20',
        ]);

        $data = $request->except(['imagen', '_token', '_method']);
        if ($request->hasFile('imagen')) {
            if ($popup->imagen) {
                Storage::disk('public')->delete($popup->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('welcome/popups', 'public');
        }
        $data['activo'] = $request->has('activo');

        $popup->update($data);

        return back()->with('success', 'Ventana emergente actualizada correctamente.');
    }

    public function destroyPopup(WelcomePopup $popup)
    {
        if ($popup->imagen) {
            Storage::disk('public')->delete($popup->imagen);
        }
        $popup->delete();

        return back()->with('success', 'Ventana emergente eliminada correctamente.');
    }

    // ============ SETTINGS ============

    public function updateSettings(Request $request)
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $clave => $valor) {
            $setting = WelcomeSetting::where('clave', $clave)->first();
            if ($setting) {
                if ($setting->tipo === 'image' && $request->hasFile("settings_files.{$clave}")) {
                    if ($setting->valor) {
                        Storage::disk('public')->delete($setting->valor);
                    }
                    $valor = $request->file("settings_files.{$clave}")->store('welcome/settings', 'public');
                }
                $setting->update(['valor' => $valor]);
            }
        }

        // Handle image uploads separately
        if ($request->hasFile('settings_files')) {
            foreach ($request->file('settings_files') as $clave => $file) {
                $setting = WelcomeSetting::where('clave', $clave)->first();
                if ($setting && $setting->tipo === 'image') {
                    if ($setting->valor) {
                        Storage::disk('public')->delete($setting->valor);
                    }
                    $setting->update(['valor' => $file->store('welcome/settings', 'public')]);
                }
            }
        }

        return back()->with('success', 'Configuracion actualizada correctamente.');
    }
}
