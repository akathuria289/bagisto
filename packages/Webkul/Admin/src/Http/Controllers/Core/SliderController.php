<?php

namespace Webkul\Admin\Http\Controllers\Core;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\DataGrids\SliderDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Core\Repositories\SliderRepository;

class SliderController extends Controller
{
    /**
     * Contains route related configuration.
     *
     * @var array
     */
    protected $_config;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Core\Repositories\SliderRepository  $sliderRepository
     * @return void
     */
    public function __construct(protected SliderRepository $sliderRepository)
    {
        $this->_config = request('_config');
    }

    /**
     * Loads the index for the sliders settings.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(SliderDataGrid::class)->toJson();
        }

        return view($this->_config['view']);
    }

    /**
     * Loads the form for creating slider.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $locale = core()->getRequestedLocaleCode();

        return view($this->_config['view'])->with('locale', $locale);
    }

    /**
     * Creates the new slider item.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'title'      => 'string|required',
            'channel_id' => 'required',
            'expired_at' => 'nullable|date',
            'image.*'    => 'required|mimes:bmp,jpeg,jpg,png,webp',
        ]);

        $data = request()->all();

        $data['expired_at'] = $data['expired_at'] ?: null;

        if (isset($data['locale'])) {
            $data['locale'] = implode(',', $data['locale']);
        }

        Event::dispatch('core.settings.slider.create.before');

        $slider = $this->sliderRepository->create($data);

        Event::dispatch('core.settings.slider.create.after', $slider);

        session()->flash('success', trans('admin::app.settings.sliders.created-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Edit the previously created slider item.
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $slider = $this->sliderRepository->findOrFail($id);

        return view($this->_config['view'])->with('slider', $slider);
    }

    /**
     * Edit the previously created slider item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate(request(), [
            'title'      => 'string|required',
            'channel_id' => 'required',
            'expired_at' => 'nullable|date',
            'image.*'    => 'sometimes|mimes:bmp,jpeg,jpg,png,webp',
        ]);

        $data = request()->all();

        $data['expired_at'] = $data['expired_at'] ?: null;

        if (isset($data['locale'])) {
            $data['locale'] = implode(',', $data['locale']);
        }

        if (is_null(request()->image)) {
            session()->flash('error', trans('admin::app.settings.sliders.update-fail'));

            return redirect()->back();
        }

        Event::dispatch('core.settings.slider.update.before', $id);

        $slider = $this->sliderRepository->update($data, $id);

        Event::dispatch('core.settings.slider.update.after', $slider);

        session()->flash('success', trans('admin::app.settings.sliders.update-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Delete the slider item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->sliderRepository->findOrFail($id);

        try {
            Event::dispatch('core.settings.slider.delete.before', $id);

            $this->sliderRepository->delete($id);

            Event::dispatch('core.settings.slider.delete.after', $id);

            return response()->json(['message' => trans('admin::app.response.delete-success', ['name' => 'Slider'])]);
        } catch (\Exception $e) {
            report($e);
        }

        return response()->json(['message' => trans('admin::app.response.delete-failed', ['name' => 'Slider'])], 500);
    }

    /**
     * Remove the specified resources from database.
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy()
    {
        $suppressFlash = false;

        if (request()->isMethod('post')) {
            $indexes = explode(',', request()->input('indexes'));

            foreach ($indexes as $key => $value) {
                try {
                    Event::dispatch('core.settings.slider.delete.before', $value);

                    $this->sliderRepository->delete($value);

                    Event::dispatch('core.settings.slider.delete.after', $value);
                } catch (\Exception $e) {
                    $suppressFlash = true;

                    continue;
                }
            }

            if (! $suppressFlash) {
                session()->flash('success', trans('admin::app.datagrid.mass-ops.delete-success', ['resource' => 'sliders']));
            } else {
                session()->flash('info', trans('admin::app.datagrid.mass-ops.partial-action', ['resource' => 'sliders']));
            }

            return redirect()->back();
        } else {
            session()->flash('error', trans('admin::app.datagrid.mass-ops.method-error'));

            return redirect()->back();
        }
    }
}
