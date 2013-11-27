<?php

class eZShop_Monitoring {
    static function testTemporaryOrderCount( ) {
        $lastAnalysis = eZSiteData::fetchByName( 'report_eZShop' );
        $createdFilter = '';
        if( $lastAnalysis ) {
            $lastAnalysisDate = new DateTime( $lastAnalysis->attribute( 'value' ) );
            $createdFilter = ' AND created > ' . $lastAnalysisDate->format( 'U' );
        }
        try {
            return OWMonitoringReportTools::executeCountQuery( 'ezbasket', 'is_temporary = TRUE' . $createdFilter );
        } catch( OWMonitoringReportNoValueException $e ) {
            return 0;
        }
    }

    static function testOrderCount( ) {
        $lastAnalysis = eZSiteData::fetchByName( 'report_eZShop' );
        $createdFilter = '';
        $result = array( );
        if( $lastAnalysis ) {
            $lastAnalysisDate = new DateTime( $lastAnalysis->attribute( 'value' ) );
            $createdFilter = ' AND created > ' . $lastAnalysisDate->format( 'U' );
        }
        try {
            $result['total'] = OWMonitoringReportTools::executeCountQuery( 'ezbasket', 'is_temporary = FALSE' . $createdFilter );
        } catch( OWMonitoringReportNoValueException $e ) {
            $result['total'] = 0;
        }
        $orderStatusList = eZOrderStatus::fetchList( );
        $trans = eZCharTransform::instance( );
        foreach( $orderStatusList as $orderStatus ) {
            $statusName = $trans->transformByGroup( $orderStatus->attribute( 'name' ), 'identifier' );
            try {
                $result[$statusName] = OWMonitoringReportTools::executeCountQuery( 'ezbasket', 'is_temporary = FALSE AND status_id = ' . $orderStatus->attribute( 'status_id' ) . $createdFilter );
            } catch( OWMonitoringReportNoValueException $e ) {
                $result[$statusName] = 0;
            }
        }
        return $result;
    }

}
