<?php

namespace App\DataTables;

use App\Models\RideRequest;
use App\Models\User;
use App\Models\WithdrawRequest;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use App\Traits\DataTableTrait;

class WithdrawRequestDataTable extends DataTable
{
    use DataTableTrait;
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $totalamount = 0;
        return datatables()
            ->eloquent($query)
            // ->editColumn('status', function($query) {
            //     $status = 'warning';
            //     switch ($query->status) {
            //         case 0:
            //             $status = 'primary  ';
            //             $status_label =  __('message.requested');
            //             break;
            //         case 1:
            //             $status = 'success';
            //             $status_label =  __('message.approved');
            //             break;
            //         case 2:
            //             $status = 'danger';
            //             $status_label =  __('message.decline');
            //             break;
            //         default:
            //             $status_label = null;
            //             break;
            //     }
            //     return '<span class="text-capitalize badge bg-'.$status.'">'.$status_label.'</span>';
            // })
            ->editColumn('name' , function ( $query ) {
                $res = $query->driverRideRequestDetail->first();
                if(isset($res))
                {
                    $user = User::where('id',$res->driver_id)->first();
                    return $user->first_name.' '. $user->last_name;
                }
            })
            ->editColumn('total_amount' , function ( $query ) {
                $res = $query->driverRideRequestDetail->first();
                if(isset($res))
                {
                   return $query->driverRideRequestDetail->sum('total_amount');
                }
                else
                {
                    return 0;
                }
              
            })
            // ->editColumn('user_id' , function ( $query ) {
            //     return $query->user_id != null ? optional($query->user)->display_name : '';
            // })

            // ->filterColumn('user_id', function( $query, $keyword ){
            //     $query->whereHas('user', function ($q) use($keyword){
            //         $q->where('display_name', 'like' , '%'.$keyword.'%');
            //     });
            // })
            ->editColumn('created_at', function ($query) {
                return dateAgoFormate($query->created_at, true);
            })
            ->addIndexColumn()
            
            // ->addColumn('action', function($query){
            //     return view('withdrawrequest.action',compact('query'))->render();
            // })
            ->rawColumns(['name','total_amount' ]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\WithdrawRequest $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $model = User::with('driverRideRequestDetail')->orderBy('id','desc');
        return $this->applyScopes($model);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('DT_RowIndex')
                ->searchable(false)
                ->title(__('message.srno'))
                ->orderable(false)
                ->width(60),
            // Column::make('user_id')->title( __('message.name') ),
            Column::make('name')->title( __('message.name') ),
            // Column::make('created_at')->title( __('message.created_at') ),
            // Column::make('status')->title( __('message.status') ),
          
            Column::make('total_amount')->title( __('message.amount') ),
            // Column::computed('action')
            //       ->exportable(false)
            //       ->printable(false)
            //       ->width(60)
            //       ->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'withdraw_request_' . date('YmdHis');
    }
}
