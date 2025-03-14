<?php
namespace App\Http\Middleware;

use App\DataModel\Manager\SessionManager;
use App\DataModel\Manager\UserManager;
use Closure;

class CheckRole{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    private $haveRoles;
    public function handle($request, Closure $next)
    {
        // Get the required roles from the route
        $roles = $this->getRequiredRoleForRoute($request->route());
        // Check if a role is required for the route, and
        // if so, ensure that the user has that role.
        $userId = $request->get('userId');
        $shopId = $request->get('shopId');
        if($this->hasRole($roles, $userId, $shopId) || !$roles)
        {
            $isHostAttendant = $this->checkIfUserHasRole(['attendant']);
            $userRoleCheckData = $this->getCashCounterRole(['app-user']);

            (new SessionManager())->setSession('cashCounterUser', $userRoleCheckData);

            if($userRoleCheckData['isCashCounterUser']){
                if($request->route()->getAction()['prefix'] == "/sales" || $request->route()->getAction()['prefix'] == "/merchant"){
                    return redirect('/sales/order');
                }
                if(!$userRoleCheckData['isOtherRoleExists']){
                    if(strpos($request->route()->getAction()['prefix'], "sales") !== false || strpos($request->route()->getAction()['prefix'], "inventory") !== false || strpos($request->route()->getAction()['prefix'], "accounting") !== false || strpos($request->route()->getAction()['prefix'], "online-shop") !== false ||  $request->route()->getAction()['prefix'] == "api/merchant" || $request->route()->getAction()['prefix'] == "api/kitchen"){
                        if($request->route()->getAction()['prefix'] == "sales/order" || $request->route()->getAction()['prefix'] == "sales/report" || $request->route()->getAction()['prefix'] == "sales/report/item-wise" || $request->route()->getAction()['prefix'] == "api/merchant" || $request->route()->getAction()['prefix'] == "api/kitchen"){
                            $request->merge(array("isHostAttendant" => $isHostAttendant));
                            return $next($request);
                        }
                        else{
                            return abort(401,'INSUFFICIENT_ACCESS , You are not authorized to access this resource.');
                        }
                    }
                }
                else{
                    $request->merge(array("isHostAttendant" => $isHostAttendant));
                    return $next($request);
                }
            }
            else{
                $request->merge(array("isHostAttendant" => $isHostAttendant));
                return $next($request);
            }
        }
        $this->redirectToUrl($request,'INSUFFICIENT_ACCESS , You are not authorized to access this resource.',401);

        if($userId && $shopId)
        {
            if(strpos($request->route()->getAction()['prefix'] , 'admin') !== FALSE)
            {
                return abort(403,'INSUFFICIENT_ACCESS , You are not authorized to access this resource.');
            }
            //routing based on permission
            if(strpos($request->route()->getAction()['prefix'] , 'sales') !== FALSE)
            {
                return redirect('/inventory');
            }
            else if(strpos($request->route()->getAction()['prefix'] , 'inventory') !== FALSE)
            {
                return redirect('/accounting');
            }
            else if(strpos($request->route()->getAction()['prefix'] , 'accounting') !== FALSE)
            {
                return redirect('/online-shop');
            }
            else
            {
                return redirect('/sales');
            }
        }
        return redirect('/account/manage');
        //return abort(401,"INSUFFICIENT_ACCESS , You are not authorized to access this resource.");
    }
    public static function redirectToUrl($request,$message,$statusCode){
        if($request->isJson() || $request->ajax()){
            response()->json(['status'=>false,'data'=>[],'message_bag'=>$message],$statusCode)->send();
            exit();
        }
    }
    private function getRequiredRoleForRoute($route)
    {
        $actions = $route->getAction();
        return isset($actions['roles']) ? $actions['roles'] : null;
    }
    private function hasRole($roles, $userId, $shopId)
    {
        $this->haveRoles = $this->getUserRole($userId, $shopId);
        // Check if the user is a root account
        if($this->checkIfUserHasRole(['root'])) {
            return true;
        }

        if(is_array($roles)){
            return $this->checkIfUserHasRole($roles);
        }
        return false;
    }
    private function getUserRole($userId, $shopId)
    {
        return (new UserManager())->getRoleResults($userId, $shopId);
    }
    private function checkIfUserHasRole($needRoles)
    {
        return array_intersect($needRoles,$this->haveRoles) ? true : false;
    }

    private function getCashCounterRole($needRoles)
    {
        $elementsToRemove = array("app-kitchen", "attendant");

        foreach ($elementsToRemove as $element) {
            $key = array_search($element, $this->haveRoles);
            if ($key !== false) {
                unset($this->haveRoles[$key]);
            }
        }

        $rolesArray = array_values($this->haveRoles);
        $otherRoleExists = false;
        if(count($rolesArray)>1){
            $otherRoleExists = true;
        }

        $isSalesModuleUser = (bool)array_intersect(["sales-module"], $rolesArray);
        if($isSalesModuleUser){
            $cashCounterUser = false;
        }
        else{
            $cashCounterUser = (bool)array_intersect($needRoles, $rolesArray);
        }

        return array('isCashCounterUser'=>$cashCounterUser, 'isOtherRoleExists'=>$otherRoleExists);
    }
}