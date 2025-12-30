define([
    'ko',
    'domReady!'
], function (ko) {
    'use strict';

    var rewardPointsBalanceQty = ko.observable(0),
        canApplyRewardPoints = ko.observable(false),
        rewardPointsMaxAllowedQtyToApply = ko.observable(0),
        rewardPointsConversionRatePointToCurrencyValue = ko.observable(0),
        areRewardPointsApplied = ko.observable(false),
        appliedRewardPointsQty = ko.observable(0),
        appliedRewardPointsAmount = ko.observable(0),
        rewardPointsTabLabelName =  ko.observable('Reward Points'),
        rewardPointsLabelName =  ko.observable('Reward Points')
    ;

    return {
        rewardPointsBalanceQty: rewardPointsBalanceQty,
        canApplyRewardPoints: canApplyRewardPoints,
        rewardPointsMaxAllowedQtyToApply: rewardPointsMaxAllowedQtyToApply,
        rewardPointsConversionRatePointToCurrencyValue: rewardPointsConversionRatePointToCurrencyValue,
        areRewardPointsApplied: areRewardPointsApplied,
        appliedRewardPointsQty: appliedRewardPointsQty,
        appliedRewardPointsAmount: appliedRewardPointsAmount,
        rewardPointsTabLabelName: rewardPointsTabLabelName,
        rewardPointsLabelName: rewardPointsLabelName,

        /**
         * @return {*}
         */
        getRewardPointsBalanceQty: function () {
            return rewardPointsBalanceQty();
        },

        /**
         * @param {*} rewardPointsBalanceQtyValue
         */
        setRewardPointsBalanceQty: function (rewardPointsBalanceQtyValue) {
            rewardPointsBalanceQty(rewardPointsBalanceQtyValue);
        },

        /**
         * @return {Boolean}
         */
        getCanApplyRewardPoints: function () {
            return canApplyRewardPoints();
        },

        /**
         * @param {Boolean} canApplyRewardPointsFlag
         */
        setCanApplyRewardPoints: function (canApplyRewardPointsFlag) {
            canApplyRewardPoints(canApplyRewardPointsFlag);
        },

        /**
         * @return {*}
         */
        getRewardPointsMaxAllowedQtyToApply: function () {
            return rewardPointsMaxAllowedQtyToApply();
        },

        /**
         * @param {*} rewardPointsMaxAllowedQtyToApplyValue
         */
        setRewardPointsMaxAllowedQtyToApply: function (rewardPointsMaxAllowedQtyToApplyValue) {
            rewardPointsMaxAllowedQtyToApply(rewardPointsMaxAllowedQtyToApplyValue);
        },

        /**
         * @return {*}
         */
        getRewardPointsConversionRatePointToCurrencyValue: function () {
            return rewardPointsConversionRatePointToCurrencyValue();
        },

        /**
         * @param {*} rewardPointsConversionRatePointToCurrencyNewValue
         */
        setRewardPointsConversionRatePointToCurrencyValue: function (rewardPointsConversionRatePointToCurrencyNewValue) {
            rewardPointsConversionRatePointToCurrencyValue(rewardPointsConversionRatePointToCurrencyNewValue);
        },

        /**
         * @return {Boolean}
         */
        getAreRewardPointsApplied: function () {
            return areRewardPointsApplied();
        },

        /**
         * @param {Boolean} areRewardPointsAppliedFlag
         */
        setAreRewardPointsApplied: function (areRewardPointsAppliedFlag) {
            areRewardPointsApplied(areRewardPointsAppliedFlag);
        },

        /**
         * @return {*}
         */
        getAppliedRewardPointsQty: function () {
            return appliedRewardPointsQty();
        },

        /**
         * @param {*} appliedRewardPointsQtyValue
         */
        setAppliedRewardPointsQty: function (appliedRewardPointsQtyValue) {
            appliedRewardPointsQty(appliedRewardPointsQtyValue);
        },

        /**
         * @return {*}
         */
        getAppliedRewardPointsAmount: function () {
            return appliedRewardPointsAmount();
        },

        /**
         * @param {*} appliedRewardPointsAmountValue
         */
        setAppliedRewardPointsAmount: function (appliedRewardPointsAmountValue) {
            appliedRewardPointsAmount(appliedRewardPointsAmountValue);
        },

        /**
         * Retrieve reward points label name
         *
         * @return {*}
         */
        getRewardPointsLabelName: function () {
            return rewardPointsLabelName();
        },

        /**
         * Set reward points label name
         *
         * @param {*} rewardPointsLabelNameValue
         */
        setRewardPointsLabelName: function (rewardPointsLabelNameValue) {
            rewardPointsLabelName(rewardPointsLabelNameValue);
        },

        /**
         * Retrieve reward points tab label name
         *
         * @return {*}
         */
        getRewardPointsTabLabelName: function () {
            return rewardPointsTabLabelName();
        },

        /**
         * Set reward points tab label name
         *
         * @param {*} rewardPointsTabLabelNameValue
         */
        setRewardPointsTabLabelName: function (rewardPointsTabLabelNameValue) {
            rewardPointsTabLabelName(rewardPointsTabLabelNameValue);
        }
    };
});
